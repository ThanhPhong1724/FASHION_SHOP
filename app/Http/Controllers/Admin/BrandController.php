<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
        ]);

        if ($request->hasFile('logo')) {
            $brand->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        return redirect()->route('admin.brands.index')->with('success', 'Thương hiệu đã được tạo thành công.');
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
        ]);

        if ($request->hasFile('logo')) {
            $brand->clearMediaCollection('logos');
            $brand->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        return redirect()->route('admin.brands.index')->with('success', 'Thương hiệu đã được cập nhật thành công.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Thương hiệu đã được xóa thành công.');
    }
}
