@extends('layouts.app')

@section('title', $brand->name . ' - Fashion Shop')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Sản phẩm</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $brand->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Brand Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                @if($brand->getFirstMediaUrl('logos'))
                    <img src="{{ $brand->getFirstMediaUrl('logos', 'preview') }}" 
                         alt="{{ $brand->name }}" 
                         class="h-16 w-16 rounded-lg object-contain bg-white border">
                @else
                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 font-bold text-lg">{{ substr($brand->name, 0, 2) }}</span>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $brand->name }}</h1>
                    @if($brand->description)
                        <p class="mt-2 text-lg text-gray-600">{{ $brand->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bộ lọc</h3>
                    
                    <form method="GET" action="{{ route('brands.show', $brand) }}" id="filter-form">
                        <!-- Search -->
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Danh mục</h4>
                            <div class="space-y-2">
                                @foreach($categories as $category)
                                    <label class="flex items-center">
                                        <input type="radio" name="category" value="{{ $category->id }}" 
                                               {{ request('category') == $category->id ? 'checked' : '' }}
                                               class="filter-input rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }} ({{ $category->products_count }})</span>
                                    </label>
                                @endforeach
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="" 
                                           {{ !request('category') ? 'checked' : '' }}
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

                        <!-- Clear Filters -->
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                Áp dụng
                            </button>
                            <a href="{{ route('brands.show', $brand) }}" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400 text-center">
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
                            <option value="name_asc" {{ request('name_asc') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Nổi bật</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
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
                                <div class="p-4">
                                    <div class="text-sm text-gray-500 mb-1">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </div>
                                    
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ $product->short_description }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            @if($product->sale_price)
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
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 text-center block">
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
