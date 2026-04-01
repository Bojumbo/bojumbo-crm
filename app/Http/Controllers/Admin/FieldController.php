<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldMetadata;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $entity = $request->get('entity', 'deal');
        $fields = FieldMetadata::where('entity', $entity)
            ->orderBy('static_id')
            ->get();

        return view('admin.fields.index', compact('fields', 'entity'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity' => 'required|string',
            'label_en' => 'required|string',
            'label_uk' => 'required|string',
            'field_type' => 'required|string',
        ]);

        // Геренація наступного Static ID для кастомних полів (10000+)
        $lastCustomField = FieldMetadata::where('static_id', '>=', 10000)
            ->orderBy('static_id', 'desc')
            ->first();

        $nextId = $lastCustomField ? $lastCustomField->static_id + 1 : 10000;

        FieldMetadata::create([
            'static_id' => $nextId,
            'entity' => $request->entity,
            'field_key' => \Illuminate\Support\Str::snake($request->label_en),
            'field_type' => $request->field_type,
            'is_system' => false,
            'label_en' => $request->label_en,
            'label_uk' => $request->label_uk,
            'label_he' => $request->label_he ?? $request->label_en,
        ]);

        return redirect()->back()->with('success', 'Field created with ID ' . $nextId);
    }

    public function destroy(FieldMetadata $field)
    {
        if ($field->is_system) {
            return redirect()->back()->with('error', 'Cannot delete system fields');
        }
        $field->delete();
        return redirect()->back();
    }
}
