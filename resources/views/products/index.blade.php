@extends('layouts.app')

@section('title', 'Sản phẩm - Fashion Shop')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Sản phẩm</h1>
            <p class="mt-2 text-gray-600">Khám phá bộ sưu tập thời trang mới nhất</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bộ lọc</h3>
                    
                    <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                        <!-- Search -->
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Danh mục</h4>
                            <div class="space-y-2">
                                @foreach($categories as $category)
                                    <div>
                                        <label class="flex items-center">
                                            <input type="radio" name="category" value="{{ $category->id }}" 
                                                   {{ request('category') == $category->id ? 'checked' : '' }}
                                                   class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                        </label>
                                        @if($category->children->count() > 0)
                                            <div class="ml-6 mt-1 space-y-1">
                                                @foreach($category->children as $child)
                                                    <label class="flex items-center">
                                                        <input type="radio" name="category" value="{{ $child->id }}" 
                                                               {{ request('category') == $child->id ? 'checked' : '' }}
                                                               class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="ml-2 text-sm text-gray-600">{{ $child->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="" 
                                           {{ !request('category') ? 'checked' : '' }}
                                           class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Tất cả</span>
                                </label>
                            </div>
                        </div>

                        <!-- Brands -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Thương hiệu</h4>
                            <div class="space-y-2">
                                @foreach($brands as $brand)
                                    <label class="flex items-center">
                                        <input type="radio" name="brand" value="{{ $brand->id }}" 
                                               {{ request('brand') == $brand->id ? 'checked' : '' }}
                                               class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $brand->name }} ({{ $brand->products_count }})</span>
                                    </label>
                                @endforeach
                                <label class="flex items-center">
                                    <input type="radio" name="brand" value="" 
                                           {{ !request('brand') ? 'checked' : '' }}
                                           class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Tất cả</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Khoảng giá</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Từ</label>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                           placeholder="{{ number_format($priceRange['min']) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Đến</label>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                           placeholder="{{ number_format($priceRange['max']) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Size Filter -->
                        @if(isset($sizes) && $sizes->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Kích thước</h4>
                                <div class="space-y-2">
                                    @foreach($sizes as $size)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="size[]" value="{{ $size }}" 
                                                   {{ in_array($size, (array) request('size', [])) ? 'checked' : '' }}
                                                   class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $size }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Color Filter -->
                        @if(isset($colors) && $colors->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Màu sắc</h4>
                                <div class="space-y-2">
                                    @foreach($colors as $color)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="color[]" value="{{ $color }}" 
                                                   {{ in_array($color, (array) request('color', [])) ? 'checked' : '' }}
                                                   class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $color }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Additional Filters -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Khác</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="featured" value="1" 
                                           {{ request('featured') ? 'checked' : '' }}
                                           class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="in_stock" value="1" 
                                           {{ request('in_stock') ? 'checked' : '' }}
                                           class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Còn hàng</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="on_sale" value="1" 
                                           {{ request('on_sale') ? 'checked' : '' }}
                                           class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Đang giảm giá</span>
                                </label>
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                Áp dụng
                            </button>
                            <a href="{{ route('products.index') }}" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400 text-center">
                                Xóa
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <!-- Sort & Results -->
                <div class="flex justify-between items-center mb-6">
                    <div class="text-sm text-gray-700">
                        Hiển thị {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                        trong {{ $products->total() }} sản phẩm
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-700">Sắp xếp:</label>
                        <select name="sort" class="sort-select border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Nổi bật</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow flex flex-col h-full">
                                <!-- Product Image -->
                                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                                    @if($product->getFirstMediaUrl('images'))
                                        <img src="{{ $product->getFirstMediaUrl('images', 'preview') }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-64 w-full object-cover object-center">
                                    @else
                                        <div class="h-64 w-full flex items-center justify-center bg-gray-200">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($product->is_featured)
                                        <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">
                                            Nổi bật
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-4 flex flex-col flex-grow">
                                    <div class="text-sm text-gray-500 mb-1">
                                        {{ $product->category->name ?? 'N/A' }}
                                        @if($product->brand)
                                            • {{ $product->brand->name }}
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-3 flex-grow">
                                        {{ $product->short_description }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-2">
                                            @if($product->sale_price && $product->sale_price < $product->base_price)
                                                <span class="text-lg font-bold text-red-600">₫{{ number_format($product->sale_price) }}</span>
                                                <span class="text-sm text-gray-500 line-through">₫{{ number_format($product->base_price) }}</span>
                                            @else
                                                <span class="text-lg font-bold text-gray-900">₫{{ number_format($product->base_price) }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="text-xs text-gray-500">
                                            {{ $product->variants_count }} biến thể
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 text-center block transition-colors">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy sản phẩm</h3>
                        <p class="mt-1 text-sm text-gray-500">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Xem tất cả sản phẩm
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });

    // Auto-submit form when sort changes
    document.querySelector('.sort-select').addEventListener('change', function() {
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = window.location.pathname;
        
        // Add current search params
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('sort', this.value);
        
        // Add all params as hidden inputs
        for (const [key, value] of urlParams) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    });
});
</script>
@endsection
