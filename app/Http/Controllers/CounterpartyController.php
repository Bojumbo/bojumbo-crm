<?php

namespace App\Http\Controllers;

use App\Models\Counterparty;
use App\Models\FieldMetadata;
use Illuminate\Http\Request;

class CounterpartyController extends Controller
{
    public function index()
    {
        $columns = FieldMetadata::where('entity', 'counterparty')
            ->orderBy('static_id')
            ->get();

        $counterparties = Counterparty::with('fieldValues')->latest()->get();

        return view('counterparties.index', compact('counterparties', 'columns'));
    }

    public function store(Request $request)
    {
        $counterparty = Counterparty::create([
            'type' => $request->input('fields.1004') ?? 'individual',
        ]);

        $counterparty->saveDynamicFields($request->input('fields', []));

        return redirect()->route('counterparties.index');
    }

    public function update(Request $request, Counterparty $counterparty)
    {
        $counterparty->update([
            'type' => $request->input('fields.1004') ?? $counterparty->type,
        ]);

        // Оновлюємо динамічні поля: автоматично порівнюємо та оновлюємо лише змінені
        $counterparty->saveDynamicFields($request->input('fields', []));

        return redirect()->route('counterparties.index');
    }

    public function destroy(Counterparty $counterparty)
    {
        $counterparty->delete(); // Soft Delete
        return redirect()->route('counterparties.index');
    }

    public function quickStore(Request $request)
    {
        $counterparty = Counterparty::create([
            'type' => 'individual',
        ]);

        $counterparty->saveDynamicFields($request->input('fields', []));

        return response()->json([
            'success' => true,
            'id' => $counterparty->id,
            'name' => $counterparty->getFieldValue(1001) ?? 'New Customer'
        ]);
    }

}
