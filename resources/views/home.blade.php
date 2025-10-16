@extends('layouts.app')

@section('title', 'Trang chủ - Fashion Shop')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Thời trang hiện đại
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                Khám phá bộ sưu tập mới nhất với những thiết kế độc đáo
            </p>
            <a href="{{ route('products.index') }}" 
               class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                Mua sắm ngay
            </a>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Danh mục sản phẩm</h2>
            <p class="text-lg text-gray-600">Tìm kiếm theo sở thích của bạn</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" 
                   class="group bg-white rounded-lg shadow-sm border hover:shadow-lg transition-shadow">
                    <div class="aspect-w-16 aspect-h-9 w-full overflow-hidden rounded-t-lg bg-gray-200">
                        @if($category->getFirstMediaUrl('images'))
                            <img src="{{ $category->getFirstMediaUrl('images', 'preview') }}" 
                                 alt="{{ $category->name }}" 
                                 class="h-48 w-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="h-48 w-full flex items-center justify-center bg-gray-200">
                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 mb-3">{{ $category->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $category->products_count }} sản phẩm</span>
                            <span class="text-indigo-600 font-medium group-hover:text-indigo-700">Xem thêm →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Featured Products Section -->
@if($featuredProducts->count() > 0)
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Sản phẩm nổi bật</h2>
            <p class="text-lg text-gray-600">Những sản phẩm được yêu thích nhất</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
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
                        
                        <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">
                            Nổi bật
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">
                            {{ $product->category->name ?? 'N/A' }}
                            @if($product->brand)
                                • {{ $product->brand->name }}
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($product->sale_price)
                                    <span class="text-lg font-bold text-red-600">₫{{ number_format($product->sale_price) }}</span>
                                    <span class="text-sm text-gray-500 line-through">₫{{ number_format($product->base_price) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">₫{{ number_format($product->base_price) }}</span>
                                @endif
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
        
        <div class="text-center mt-8">
            <a href="{{ route('products.index', ['featured' => 1]) }}" 
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                Xem tất cả sản phẩm nổi bật
            </a>
        </div>
    </div>
</div>
@endif

<!-- Latest Products Section -->
@if($latestProducts->count() > 0)
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Sản phẩm mới nhất</h2>
            <p class="text-lg text-gray-600">Những sản phẩm vừa được thêm vào</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($latestProducts as $product)
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
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">
                            {{ $product->category->name ?? 'N/A' }}
                            @if($product->brand)
                                • {{ $product->brand->name }}
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($product->sale_price)
                                    <span class="text-lg font-bold text-red-600">₫{{ number_format($product->sale_price) }}</span>
                                    <span class="text-sm text-gray-500 line-through">₫{{ number_format($product->base_price) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">₫{{ number_format($product->base_price) }}</span>
                                @endif
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
        
        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" 
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</div>
@endif

<!-- Brands Section -->
@if($brands->count() > 0)
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Thương hiệu nổi bật</h2>
            <p class="text-lg text-gray-600">Những thương hiệu được tin tưởng</p>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-6">
            @foreach($brands as $brand)
                <a href="{{ route('brands.show', $brand) }}" 
                   class="group bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow p-4 text-center">
                    @if($brand->getFirstMediaUrl('logos'))
                        <img src="{{ $brand->getFirstMediaUrl('logos', 'thumb') }}" 
                             alt="{{ $brand->name }}" 
                             class="h-16 w-16 mx-auto mb-2 object-contain">
                    @else
                        <div class="h-16 w-16 mx-auto mb-2 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-400 font-bold text-lg">{{ substr($brand->name, 0, 2) }}</span>
                        </div>
                    @endif
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">{{ $brand->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $brand->products_count }} sản phẩm</p>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
