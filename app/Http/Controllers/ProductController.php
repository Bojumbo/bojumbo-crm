<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FieldMetadata;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $columns = FieldMetadata::where('entity', 'product')
            ->orderBy('static_id')
            ->get();

        $products = Product::with('fieldValues')->latest()->get();

        return view('products.index', compact('products', 'columns'));
    }

    public function store(Request $request)
    {
        $product = Product::create();
        $this->saveFields($product, $request->input('fields', []));
        return redirect()->route('products.index');
    }

    public function update(Request $request, Product $product)
    {
        $product->fieldValues()->delete();
        $this->saveFields($product, $request->input('fields', []));
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }

    public function quickStore(Request $request)
    {
        $product = Product::create();
        $this->saveFields($product, $request->input('fields', []));
        
        // Перезавантажуємо зв'язки, щоб getFieldValue спрацював
        $product->load('fieldValues');

        return response()->json([
            'success' => true,
            'id' => $product->id,
            'name' => $product->getFieldValue(3001) ?? 'New Product',
            'price' => $product->getFieldValue(3003) ?? 0,
            'field_values' => [['static_id' => 3003, 'value' => $product->getFieldValue(3003) ?? 0]]
        ]);
    }

    protected function saveFields(Product $product, array $fields)
    {
        foreach ($fields as $staticId => $value) {
            if ($value !== null) {
                $product->fieldValues()->create([
                    'static_id' => $staticId,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        }
    }
}
