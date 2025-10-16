@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Thêm sản phẩm mới</h1>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Quay lại
                    </a>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- General Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin chung</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>

                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Sẽ tự động tạo nếu để trống">
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục *</label>
                                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="brand_id" class="block text-sm font-medium text-gray-700">Thương hiệu</label>
                                <select name="brand_id" id="brand_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Để trống để tự động tạo">
                                <p class="text-sm text-gray-500 mt-1">Mã quản lý hàng tồn kho. Để trống sẽ tự động tạo theo format: CAT-BRAND-NAME-YYYYMMDD-XXXX</p>
                            </div>

                            <div>
                                <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                                <select name="tags[]" id="tags" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Mô tả</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="short_description" class="block text-sm font-medium text-gray-700">Mô tả ngắn</label>
                                <textarea name="short_description" id="short_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('short_description') }}</textarea>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                                <textarea name="description" id="description" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Giá cả</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="base_price" class="block text-sm font-medium text-gray-700">Giá gốc *</label>
                                <input type="number" name="base_price" id="base_price" value="{{ old('base_price') }}" min="0" step="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>

                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700">Giá khuyến mãi</label>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" min="0" step="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Hình ảnh</h3>
                        <div>
                            <label for="images" class="block text-sm font-medium text-gray-700">Upload hình ảnh</label>
                            <input type="file" name="images[]" id="images" accept="image/*" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-sm text-gray-500 mt-1">Có thể chọn nhiều ảnh cùng lúc</p>
                        </div>
                    </div>

                    <!-- Variants -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Biến thể sản phẩm</h3>
                        <div id="variants-container">
                            <div class="variant-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Size</label>
                                        <input type="text" name="variants[0][size]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="S, M, L, XL">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Màu sắc</label>
                                        <input type="text" name="variants[0][color]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Đen, Trắng, Xanh">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                                        <input type="text" name="variants[0][sku]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                                        <input type="number" name="variants[0][stock]" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Điều chỉnh giá</label>
                                        <input type="number" name="variants[0][price_delta]" step="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-variant" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Thêm biến thể
                        </button>
                    </div>

                    <!-- SEO -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700">SEO Title</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700">SEO Description</label>
                                <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Trạng thái</h3>
                        <div class="flex space-x-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Kích hoạt
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                    Sản phẩm nổi bật
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tạo sản phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantIndex = 1;
    
    document.getElementById('add-variant').addEventListener('click', function() {
        const container = document.getElementById('variants-container');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-item border border-gray-200 rounded-lg p-4 mb-4';
        newVariant.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h4 class="text-sm font-medium text-gray-700">Biến thể ${variantIndex + 1}</h4>
                <button type="button" class="remove-variant text-red-600 hover:text-red-800 text-sm">Xóa</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Size</label>
                    <input type="text" name="variants[${variantIndex}][size]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="S, M, L, XL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Màu sắc</label>
                    <input type="text" name="variants[${variantIndex}][color]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Đen, Trắng, Xanh">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="variants[${variantIndex}][sku]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                    <input type="number" name="variants[${variantIndex}][stock]" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Điều chỉnh giá</label>
                    <input type="number" name="variants[${variantIndex}][price_delta]" step="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="0">
                </div>
            </div>
        `;
        
        container.appendChild(newVariant);
        variantIndex++;
        
        // Add remove functionality
        newVariant.querySelector('.remove-variant').addEventListener('click', function() {
            newVariant.remove();
        });
    });
});
</script>
@endsection
