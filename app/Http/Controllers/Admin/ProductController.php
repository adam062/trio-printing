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
            'color_options' => ['nullable', 'array'],
            'color_options.*' => ['string', 'max:80'],
            'color_options_free' => ['nullable', 'string', 'max:500'],
            'color_label' => ['nullable', 'string', 'max:120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'filament_id' => ['nullable', 'exists:filaments,id'],
            'weight_grams' => ['required', 'numeric', 'min:1', 'max:100000'],
            'multiplier' => ['required', 'numeric', 'min:1', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        $filament = ! empty($data['filament_id']) ? Filament::find($data['filament_id']) : null;
        $gramCost = $filament ? (float) $filament->cost_per_gram : 0.675;

        $weight = (float) $data['weight_grams'];
        $multiplier = (float) $data['multiplier'];

        $gallery = $this->storeUploadedImages($request);

        Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'details' => $data['details'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'color_options' => $this->resolveColorOptions($request, $filament),
            'color_label' => ($data['color_label'] ?? null) ?: 'اللون المتاح',
            'image_path' => $gallery[0] ?? null,
            'gallery_images' => $gallery === [] ? null : $gallery,
            'filament_id' => $filament?->id,
            'weight_grams' => $weight,
            'base_price' => Product::basePrice($weight, $gramCost),
            'multiplier' => $multiplier,
            'price' => $this->resolvePrice($data, $weight, $multiplier, $gramCost),
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
            'color_options' => ['nullable', 'array'],
            'color_options.*' => ['string', 'max:80'],
            'color_options_free' => ['nullable', 'string', 'max:500'],
            'color_label' => ['nullable', 'string', 'max:120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'filament_id' => ['nullable', 'exists:filaments,id'],
            'weight_grams' => ['required', 'numeric', 'min:1', 'max:100000'],
            'multiplier' => ['required', 'numeric', 'min:1', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        $filament = ! empty($data['filament_id']) ? Filament::find($data['filament_id']) : null;
        $gramCost = $filament ? (float) $filament->cost_per_gram : 0.675;

        $weight = (float) $data['weight_grams'];
        $multiplier = (float) $data['multiplier'];

        $gallery = $this->resolveGallery($request, $product);

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'details' => $data['details'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'color_options' => $this->resolveColorOptions($request, $filament),
            'color_label' => ($data['color_label'] ?? null) ?: 'اللون المتاح',
            'image_path' => $gallery[0] ?? null,
            'gallery_images' => $gallery === [] ? null : $gallery,
            'filament_id' => $filament?->id,
            'weight_grams' => $weight,
            'base_price' => Product::basePrice($weight, $gramCost),
            'multiplier' => $multiplier,
            'price' => $this->resolvePrice($data, $weight, $multiplier, $gramCost),
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
        foreach ($product->gallery() as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $product->delete();

        return back()->with('success', 'تم حذف المنتج من المتجر.');
    }

    /**
     * Product colors must come from the filament inventory when the filament
     * manages its colors; otherwise fall back to the free-text custom list.
     *
     * @return array<int, string>|null
     */
    private function resolveColorOptions(Request $request, ?Filament $filament): ?array
    {
        if ($filament?->hasManagedColorInventory()) {
            $inventoryNames = collect($filament->colorInventory())->pluck('name')->all();
            $chosen = array_values(array_filter((array) $request->input('color_options', [])));
            $linked = array_values(array_intersect($chosen, $inventoryNames));

            return $linked === [] ? null : $linked;
        }

        $free = (string) $request->input('color_options_free', '');
        if (trim($free) === '') {
            return null;
        }

        $parsed = array_values(array_filter(array_map('trim', explode(',', str_replace('،', ',', $free)))));

        return $parsed === [] ? null : $parsed;
    }

    /**
     * Use the manually entered price when the admin provides one, otherwise
     * fall back to the calculated weight × gram-cost × multiplier price.
     *
     * @param  array<string, mixed>  $data
     */
    private function resolvePrice(array $data, float $weight, float $multiplier, ?float $gramCost): float
    {
        $manual = $data['price'] ?? null;

        if ($manual !== null && $manual !== '') {
            return round((float) $manual, 2);
        }

        return Product::calculatedPrice($weight, $multiplier, $gramCost);
    }

    /**
     * @return array<int, string>
     */
    private function storeUploadedImages(Request $request): array
    {
        return collect($request->file('images', []))
            ->map(fn ($file) => $file->store('products', 'public'))
            ->all();
    }

    /**
     * Rebuild the gallery: keep existing images minus removals, then append uploads.
     *
     * @return array<int, string>
     */
    private function resolveGallery(Request $request, Product $product): array
    {
        $existing = $product->gallery();
        $removals = array_values(array_filter((array) $request->input('remove_images', [])));

        $kept = array_values(array_filter(
            $existing,
            fn (string $path): bool => ! in_array($path, $removals, true)
        ));

        foreach ($removals as $path) {
            if (in_array($path, $existing, true) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        return array_merge($kept, $this->storeUploadedImages($request));
    }
}
