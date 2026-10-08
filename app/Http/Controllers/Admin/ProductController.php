<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filament;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('filament')->latest();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($filament = $request->input('filament')) {
            $query->where('filament_id', $filament);
        }

        $products = $query->paginate(15)->withQueryString();
        $filaments = Filament::query()->where('is_active', true)->orderBy('id')->get();

        return view('admin.products.index', [
            'products' => $products,
            'filaments' => $filaments,
            'currentSearch' => $search,
            'currentFilament' => $filament,
        ]);
    }

    public function edit(Product $product)
    {
        $filaments = Filament::query()->where('is_active', true)->orderBy('id')->get();

        return view('admin.products.edit', [
            'product' => $product,
            'filaments' => $filaments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'string', 'max:2000'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'color_options' => ['nullable', 'string', 'max:500'],
            'color_label' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'filament_id' => ['nullable', 'exists:filaments,id'],
            'weight_grams' => ['required', 'numeric', 'min:1', 'max:100000'],
            'multiplier' => ['required', 'numeric', 'min:1', 'max:50'],
        ]);

        $filament = ! empty($data['filament_id']) ? Filament::find($data['filament_id']) : null;
        $gramCost = $filament ? (float) $filament->cost_per_gram : 0.675;

        $weight = (float) $data['weight_grams'];
        $multiplier = (float) $data['multiplier'];

        $imagePath = $request->file('image') ? $request->file('image')->store('products', 'public') : null;

        $colorOptions = null;
        if (! empty($data['color_options'])) {
            $colorOptions = array_values(array_filter(array_map('trim', explode(',', str_replace('،', ',', $data['color_options'])))));
        }

        Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'details' => $data['details'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'color_options' => $colorOptions,
            'color_label' => $data['color_label'] ?: 'اللون المتاح',
            'image_path' => $imagePath,
            'filament_id' => $filament?->id,
            'weight_grams' => $weight,
            'base_price' => Product::basePrice($weight, $gramCost),
            'multiplier' => $multiplier,
            'price' => Product::calculatedPrice($weight, $multiplier, $gramCost),
            'is_active' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'تمت إضافة المنتج بنجاح.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'string', 'max:2000'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'color_options' => ['nullable', 'string', 'max:500'],
            'color_label' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'filament_id' => ['nullable', 'exists:filaments,id'],
            'weight_grams' => ['required', 'numeric', 'min:1', 'max:100000'],
            'multiplier' => ['required', 'numeric', 'min:1', 'max:50'],
        ]);

        $filament = ! empty($data['filament_id']) ? Filament::find($data['filament_id']) : null;
        $gramCost = $filament ? (float) $filament->cost_per_gram : 0.675;

        $weight = (float) $data['weight_grams'];
        $multiplier = (float) $data['multiplier'];

        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $colorOptions = null;
        if (! empty($data['color_options'])) {
            $colorOptions = array_values(array_filter(array_map('trim', explode(',', str_replace('،', ',', $data['color_options'])))));
        }

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'details' => $data['details'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'color_options' => $colorOptions,
            'color_label' => $data['color_label'] ?: 'اللون المتاح',
            'image_path' => $imagePath,
            'filament_id' => $filament?->id,
            'weight_grams' => $weight,
            'base_price' => Product::basePrice($weight, $gramCost),
            'multiplier' => $multiplier,
            'price' => Product::calculatedPrice($weight, $multiplier, $gramCost),
        ]);

        return redirect()->route('admin.products.index')->with('success', "تم تحديث بيانات المنتج {$product->name} بنجاح.");
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        $status = $product->is_active ? 'تفعيل ظهور' : 'إخفاء';

        return back()->with('success', "تم {$status} المنتج في المتجر.");
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return back()->with('success', 'تم حذف المنتج من المتجر.');
    }
}
