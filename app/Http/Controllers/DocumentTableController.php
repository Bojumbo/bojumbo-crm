<?php
namespace App\Http\Controllers;
use App\Models\DocumentTable;
use App\Models\FieldMetadata;
use Illuminate\Http\Request;
class DocumentTableController extends Controller
{
    public function index()
    {
        $tables = DocumentTable::all();
        $productFields = FieldMetadata::where('entity', 'product')->get();
        return view('admin.settings.document_tables', compact('tables', 'productFields'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:document_tables,name',
            'columns' => 'required|array|min:1',
            'styles' => 'nullable|array',
            'show_total' => 'nullable|boolean'
        ]);
        $validated['show_total'] = $request->has('show_total');

        // Handle unchecked checkboxes in the columns array
        $columns = $request->input('columns', []);
        foreach ($columns as $index => &$col) {
            $col['styles']['header_bold'] = $request->has("columns.$index.styles.header_bold");
            $col['styles']['content_bold'] = $request->has("columns.$index.styles.content_bold");
        }
        $validated['columns'] = $columns;

        DocumentTable::create($validated);
        return redirect()->back()->with('success', 'Table configuration saved!');
    }
    public function update(Request $request, DocumentTable $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:document_tables,name,' . $table->id,
            'columns' => 'required|array|min:1',
            'styles' => 'nullable|array',
            'show_total' => 'nullable|boolean'
        ]);
        $validated['show_total'] = $request->has('show_total');

        // Handle unchecked checkboxes in the columns array
        $columns = $request->input('columns', []);
        foreach ($columns as $index => &$col) {
            $col['styles']['header_bold'] = $request->has("columns.$index.styles.header_bold");
            $col['styles']['content_bold'] = $request->has("columns.$index.styles.content_bold");
        }
        $validated['columns'] = $columns;

        $table->update($validated);
        return redirect()->back()->with('success', 'Table configuration updated!');
    }
    public function destroy(DocumentTable $table)
    {
        $table->delete();
        return redirect()->back()->with('success', 'Table removed!');
    }
}