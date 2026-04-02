<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLoginNotification
{
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $ip = request()->ip();
        $date = now()->format('d.m.Y H:i:s');
        
        // Отримуємо локацію по IP (безоплатний API)
        $location = 'Unknown';
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    $location = "{$data['country']}, {$data['city']} ({$data['isp']})";
                }
            }
        } catch (\Exception $e) {
            Log::error('Location API error: ' . $e->getMessage());
        }

        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            Log::warning('Telegram credentials not found in env.');
            return;
        }

        $message = "🔐 *CRM Login Alert*\n\n";
        $message .= "👤 *User:* {$user->name} ({$user->email})\n";
        $message .= "📅 *Date:* {$date}\n";
        $message .= "🌐 *IP:* {$ip}\n";
        $message .= "📍 *Location:* {$location}\n";

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());
        }
    }
}
