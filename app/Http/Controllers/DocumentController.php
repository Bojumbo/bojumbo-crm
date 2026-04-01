<?php
namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\DocumentTemplate;
use App\Services\GoogleDocService;
use Illuminate\Http\Request;
class DocumentController extends Controller
{
    public function templates()
    {
        return response()->json(DocumentTemplate::all());
    }
    public function index()
    {
        $templates = DocumentTemplate::all();
        return view('admin.settings.templates', compact('templates'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'google_drive_id' => 'nullable|string|max:255',
            'entity_type' => 'required|in:deal',
            'orientation' => 'required|in:portrait,landscape',
            'content' => 'nullable|string'
        ]);

        if (empty($validated['google_drive_id']) && empty($validated['content'])) {
            return redirect()->back()->withErrors(['content' => __('Please provide either a Google Doc ID or HTML content.')])->withInput();
        }

        DocumentTemplate::create($validated);
        return redirect()->back()->with('success', __('Template added successfully!'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'google_drive_id' => 'nullable|string|max:255',
            'entity_type' => 'required|in:deal',
            'orientation' => 'required|in:portrait,landscape',
            'content' => 'nullable|string'
        ]);

        if (empty($validated['google_drive_id']) && empty($validated['content'])) {
            return redirect()->back()->withErrors(['content' => __('Please provide either a Google Doc ID or HTML content.')])->withInput();
        }

        $template->update($validated);
        return redirect()->back()->with('success', __('Template updated successfully!'));
    }
    public function destroy(DocumentTemplate $template)
    {
        $template->delete();
        return redirect()->back()->with('success', __('Template deleted successfully!'));
    }
    public function generate(Request $request, Deal $deal, GoogleDocService $service)
    {
        $request->validate([
            'template_id' => 'required|exists:document_templates,id'
        ]);
        try {
            $template = DocumentTemplate::findOrFail($request->template_id);

            // Передаємо весь об'єкт шаблону, щоб сервіс міг взяти його назву
            $docUrl = $service->generateFromDeal($deal, $template);
            return response()->json([
                'success' => true,
                'url' => $docUrl
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Список файлів у папці угоди
     */
    public function listFiles(Deal $deal, GoogleDocService $service)
    {
        try {
            $files = $service->listFiles($deal);
            return response()->json([
                'success' => true,
                'files' => $files
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Завантаження файлу в папку угоди
     */
    public function upload(Request $request, Deal $deal, GoogleDocService $service)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // макс 10МБ
        ]);

        try {
            $file = $service->uploadFile($deal, $request->file('file'));
            return response()->json([
                'success' => true,
                'file' => $file
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}