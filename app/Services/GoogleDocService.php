<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\FieldMetadata;
use App\Models\Counterparty;
use App\Models\DocumentTemplate;
use App\Models\DocumentTable;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class GoogleDocService
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
    }

    protected function getAccessToken($user): string
    {
        if ($user->google_token_expires_at && now()->addSeconds(60)->lessThan($user->google_token_expires_at)) {
            return $user->google_access_token;
        }

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $user->google_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            $user->update([
                'google_access_token' => $data['access_token'],
                'google_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);
            return $data['access_token'];
        }

        throw new \Exception('Помилка авторизації Google. Будь ласка, перепідключіть акаунт.');
    }

    protected function getOrCreateDealFolder(Deal $deal, string $token): string
    {
        if ($deal->google_drive_folder_id) {
            $check = Http::withToken($token)
                ->get("https://www.googleapis.com/drive/v3/files/{$deal->google_drive_folder_id}?fields=id,trashed");
            
            if ($check->successful() && !($check->json()['trashed'] ?? false)) {
                return $deal->google_drive_folder_id;
            }
        }

        $rootFolderId = Setting::get('google_drive_root_folder_id');
        $dealName = $deal->getFieldValue(2001) ?? "Deal #{$deal->id}";

        $response = Http::withToken($token)
            ->post('https://www.googleapis.com/drive/v3/files', [
                'name' => "Угода #{$deal->id} - {$dealName}",
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => $rootFolderId ? [$rootFolderId] : []
            ]);

        if (!$response->successful()) {
            throw new \Exception('Не вдалося створити папку Drive.');
        }

        $folderId = $response->json()['id'];
        $deal->update(['google_drive_folder_id' => $folderId]);

        return $folderId;
    }

    public function generateFromDeal(Deal $deal, DocumentTemplate $template): string
    {
        $user = auth()->user();
        $token = $this->getAccessToken($user);
        $dealFolderId = $this->getOrCreateDealFolder($deal, $token);

        // 1. Prepare HTML Content
        $htmlContent = $this->prepareHtmlContent($deal, $template, $token);

        // 2. Filename
        $filename = "{$template->name}-" . now()->format('Ymd-Hi');

        // 3. Upload to Drive (Converts HTML to Google Doc)
        $metadata = [
            'name' => $filename,
            'mimeType' => 'application/vnd.google-apps.document',
            'parents' => [$dealFolderId]
        ];

        $response = Http::withToken($token)
            ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json'])
            ->attach('file', $htmlContent, "doc.html", ['Content-Type' => 'text/html'])
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if (!$response->successful()) {
            throw new \Exception('Помилка генерації документа: ' . $response->body());
        }

        $fileId = $response->json()['id'];

        // 4. FORCE orientation and layout via Google Docs API
        $this->updateDocumentStyle($fileId, $token, ($template->orientation === 'landscape'));

        return "https://docs.google.com/document/d/{$fileId}/edit";
    }

    protected function updateDocumentStyle(string $fileId, string $token, bool $isLandscape): void
    {
        $pageSize = $isLandscape 
            ? ['height' => ['magnitude' => 595, 'unit' => 'PT'], 'width' => ['magnitude' => 842, 'unit' => 'PT']]
            : ['height' => ['magnitude' => 842, 'unit' => 'PT'], 'width' => ['magnitude' => 595, 'unit' => 'PT']];

        // Small margins (20pt) to maximize space
        $res = Http::withToken($token)
            ->post("https://docs.googleapis.com/v1/documents/{$fileId}:batchUpdate", [
                'requests' => [
                    [
                        'updateDocumentStyle' => [
                            'documentStyle' => [
                                'pageSize' => $pageSize,
                                'marginTop' => ['magnitude' => 20, 'unit' => 'PT'],
                                'marginBottom' => ['magnitude' => 20, 'unit' => 'PT'],
                                'marginLeft' => ['magnitude' => 20, 'unit' => 'PT'],
                                'marginRight' => ['magnitude' => 20, 'unit' => 'PT'],
                            ],
                            'fields' => 'pageSize,marginTop,marginBottom,marginLeft,marginRight'
                        ]
                    ]
                ]
            ]);
        
        \Log::info("Update document style ID: {$fileId}, Result: " . $res->status());
    }

    protected function prepareHtmlContent(Deal $deal, DocumentTemplate $template, string $token): string
    {
        // 1. Get base content
        $content = $template->content;
        if (empty($content)) {
            $exportRes = Http::withToken($token)
                ->get("https://www.googleapis.com/drive/v3/files/{$template->google_drive_id}/export", [
                    'mimeType' => 'text/html'
                ]);
            $content = $exportRes->successful() ? $exportRes->body() : "<html><body>Template Export Failed</body></html>";
        }

        // 2. Simple Tag Replacement
        $fields = FieldMetadata::all();
        foreach ($fields as $field) {
            $value = null;
            if ($field->entity === 'deal') {
                $value = $deal->getFieldValue($field->static_id);
                if ($field->static_id == 2002) $value = number_format((float)$value, 2);
            } elseif ($field->entity === 'counterparty') {
                $cpId = $deal->getFieldValue(2004);
                if ($cpId) {
                    $counterparty = Counterparty::find($cpId);
                    $value = $counterparty?->getFieldValue($field->static_id);
                }
            }
            $content = str_ireplace('{{' . $field->static_id . '}}', (string)($value ?? ''), $content);
        }
        $content = str_ireplace('{{date}}', now()->format('d.m.Y'), $content);

        // 3. Table Replacement
        preg_match_all('/{{table:([a-zA-Z0-9_-]+)}}/', $content, $matches);
        foreach ($matches[1] as $idx => $tagName) {
            $tableConfig = DocumentTable::where('name', $tagName)->first();
            if ($tableConfig) {
                $tableHtml = $this->renderTableHtml($tableConfig, $deal);
                $content = str_replace($matches[0][$idx], $tableHtml, $content);
            }
        }

        // Prepare clean HTML wrapper
        $cleanContent = preg_replace('/<(\/?)(html|head|body)[^>]*>/i', '', $content);

        return "<html><head>
                <meta charset='utf-8'>
                <style>
                    html, body { margin: 0; padding: 0; width: 100%; }
                    table { border-collapse: collapse; width: 100% !important; border-spacing: 0; }
                    td, th { font-weight: normal !important; text-decoration: none !important; }
                    b, strong { font-weight: bold !important; }
                </style>
                </head><body>{$cleanContent}</body></html>";
    }

    protected function renderTableHtml(DocumentTable $config, Deal $deal): string
    {
        $products = $deal->products;
        $styles = $config->styles ?? [];
        
        $tableStyle = "width:100%; border-collapse:collapse; font-size:" . ($styles['font_size'] ?? 10) . "pt;";
        $headerBg = $styles['header_bg'] ?? '#ffffff';
        $borderColor = $styles['border_color'] ?? '#000000';
        $borderWidth = ($styles['border_width'] ?? 1) . "pt";
        $globalPadding = ($styles['cell_padding'] ?? 3) . "pt";

        $html = "<table style='{$tableStyle}' width='100%'>";
        
        // Header Row
        $html .= "<tr>";
        foreach ($config->columns as $col) {
            $colStyles = $col['styles'] ?? [];
            $align = $colStyles['align'] ?? 'left';
            $width = isset($colStyles['width']) ? "width:{$colStyles['width']};" : "";
            
            $pLeft = isset($colStyles['padding_left']) ? $colStyles['padding_left'] . "pt" : $globalPadding;
            $pRight = isset($colStyles['padding_right']) ? $colStyles['padding_right'] . "pt" : $globalPadding;
            $cellPadding = "padding: {$globalPadding} {$pRight} {$globalPadding} {$pLeft};";

            $headerBold = filter_var($colStyles['header_bold'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $label = $col['label'] ?? '';
            if ($headerBold) {
                $label = "<b>{$label}</b>";
            }
            $html .= "<td bgcolor='{$headerBg}' style='border:{$borderWidth} solid {$borderColor}; {$cellPadding} text-align:{$align}; {$width}'>{$label}</td>";
        }
        $html .= "</tr>";

        // Body Rows
        $grandTotal = 0;
        foreach ($products as $product) {
            $qty = $product->pivot->quantity ?? 0;
            $price = $product->pivot->price_at_sale ?? 0;
            $subtotal = $qty * $price;
            $grandTotal += $subtotal;

            $html .= "<tr>";
            foreach ($config->columns as $col) {
                $colStyles = $col['styles'] ?? [];
                $align = $colStyles['align'] ?? 'left';
                
                $pLeft = isset($colStyles['padding_left']) ? $colStyles['padding_left'] . "pt" : $globalPadding;
                $pRight = isset($colStyles['padding_right']) ? $colStyles['padding_right'] . "pt" : $globalPadding;
                $cellPadding = "padding: {$globalPadding} {$pRight} {$globalPadding} {$pLeft};";

                $contentBold = filter_var($colStyles['content_bold'] ?? false, FILTER_VALIDATE_BOOLEAN);
                
                $val = '';
                if ($col['static_id'] == 'qty') $val = (string)$qty;
                elseif ($col['static_id'] == 'subtotal') $val = number_format($subtotal, 2);
                else $val = (string)($product->getFieldValue($col['static_id']) ?? '');

                if ($contentBold) {
                    $val = "<b>{$val}</b>";
                }
                $html .= "<td style='border:{$borderWidth} solid {$borderColor}; {$cellPadding} text-align:{$align};'>{$val}</td>";
            }
            $html .= "</tr>";
        }

        // Total Row
        if ($config->show_total) {
            $html .= "<tr>";
            foreach ($config->columns as $cIdx => $col) {
                $colStyles = $col['styles'] ?? [];
                $align = $colStyles['align'] ?? 'left';
                
                $pLeft = isset($colStyles['padding_left']) ? $colStyles['padding_left'] . "pt" : $globalPadding;
                $pRight = isset($colStyles['padding_right']) ? $colStyles['padding_right'] . "pt" : $globalPadding;
                $cellPadding = "padding: {$globalPadding} {$pRight} {$globalPadding} {$pLeft};";

                $val = '';
                if ($cIdx == 0) $val = "<b>Всього</b>";
                elseif ($col['static_id'] == 'subtotal') $val = "<b>" . number_format($grandTotal, 2) . "</b>";
                
                $html .= "<td style='border:{$borderWidth} solid {$borderColor}; {$cellPadding} text-align:{$align};'>{$val}</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";
        return $html;
    }

    public function listFiles(Deal $deal): array
    {
        $user = auth()->user();
        $token = $this->getAccessToken($user);
        $folderId = $this->getOrCreateDealFolder($deal, $token);
        $response = Http::withToken($token)->get("https://www.googleapis.com/drive/v3/files", [
            'q' => "'{$folderId}' in parents and trashed = false",
            'fields' => 'files(id, name, mimeType, webViewLink, iconLink, createdTime, size)',
            'orderBy' => 'createdTime desc'
        ]);
        return $response->json()['files'] ?? [];
    }

    public function uploadFile(Deal $deal, $file): array
    {
        $user = auth()->user();
        $token = $this->getAccessToken($user);
        $folderId = $this->getOrCreateDealFolder($deal, $token);
        $metadata = ['name' => $file->getClientOriginalName(), 'parents' => [$folderId]];
        $response = Http::withToken($token)
            ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json'])
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName(), ['Content-Type' => $file->getMimeType()])
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
        return $response->json();
    }
}
