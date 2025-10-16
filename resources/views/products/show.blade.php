@extends('layouts.app')

@section('title', $product->name . ' - Fashion Shop')

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
                @if($product->category)
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('categories.show', $product->category) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">{{ $product->category->name }}</a>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image with Zoom -->
                <div class="relative group">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 cursor-zoom-in" 
                         onclick="openImageModal('{{ $product->getFirstMediaUrl('images', 'preview') }}')">
                        @if($product->getFirstMediaUrl('images'))
                            <img id="main-image" src="{{ $product->getFirstMediaUrl('images', 'preview') }}" 
                                 alt="{{ $product->name }}" 
                                 class="h-96 w-full object-cover object-center transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="h-96 w-full flex items-center justify-center bg-gray-200">
                                <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Zoom Icon -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image Counter -->
                    @if($product->getMedia('images')->count() > 1)
                        <div class="absolute top-4 right-4 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                            <span id="current-image">1</span> / {{ $product->getMedia('images')->count() }}
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Images -->
                @if($product->getMedia('images')->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->getMedia('images') as $index => $image)
                            <button onclick="changeMainImage('{{ $image->getUrl('preview') }}', {{ $index + 1 }})" 
                                    class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 border-2 transition-all duration-200 {{ $index === 0 ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-300' }}">
                                <img src="{{ $image->getUrl('thumb') }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-20 w-full object-cover object-center">
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <div class="flex justify-center space-x-4 mt-2">
                        <button onclick="previousImage()" 
                                class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                id="prev-btn">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button onclick="nextImage()" 
                                class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                id="next-btn">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Product Title & Meta -->
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        @if($product->is_featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Nổi bật</span>
                        @endif
                        <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        @if($product->category)
                            <span>Danh mục: <a href="{{ route('categories.show', $product->category) }}" class="text-indigo-600 hover:text-indigo-800">{{ $product->category->name }}</a></span>
                        @endif
                        @if($product->brand)
                            <span>Thương hiệu: <a href="{{ route('brands.show', $product->brand) }}" class="text-indigo-600 hover:text-indigo-800">{{ $product->brand->name }}</a></span>
                        @endif
                    </div>
                </div>

                <!-- Rating & Reviews -->
                <div class="flex items-center space-x-4">
                    @php
                        $avgRating = $product->approvedReviews()->avg('rating') ?? 0;
                        $reviewCount = $product->approvedReviews()->count();
                    @endphp
                    
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @elseif($i - 0.5 <= $avgRating)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <defs>
                                        <linearGradient id="half-{{ $i }}">
                                            <stop offset="50%" stop-color="currentColor"/>
                                            <stop offset="50%" stop-color="#E5E7EB"/>
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endif
                        @endfor
                        <span class="ml-2 text-sm text-gray-600">{{ number_format($avgRating, 1) }} ({{ $reviewCount }} đánh giá)</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-center space-x-4">
                    @if($product->sale_price)
                        <span class="text-3xl font-bold text-red-600">₫{{ number_format($product->sale_price) }}</span>
                        <span class="text-xl text-gray-500 line-through">₫{{ number_format($product->base_price) }}</span>
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded">
                            -{{ round((($product->base_price - $product->sale_price) / $product->base_price) * 100) }}%
                        </span>
                    @else
                        <span class="text-3xl font-bold text-gray-900">₫{{ number_format($product->base_price) }}</span>
                    @endif
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="text-gray-600">{{ $product->short_description }}</p>
                @endif

                <!-- Variants Selection -->
                @if($product->variants->count() > 0)
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Chọn biến thể:</h3>
                        
                        <form id="variant-form" class="space-y-4">
                            <!-- Size Selection -->
                            @php
                                $sizes = $product->variants->pluck('size')->filter()->unique()->sort();
                            @endphp
                            @if($sizes->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kích thước:</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($sizes as $size)
                                            <button type="button" 
                                                    class="size-option px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    data-size="{{ $size }}">
                                                {{ $size }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Color Selection -->
                            @php
                                $colors = $product->variants->pluck('color')->filter()->unique()->sort();
                            @endphp
                            @if($colors->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Màu sắc:</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($colors as $color)
                                            <button type="button" 
                                                    class="color-option px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    data-color="{{ $color }}">
                                                {{ $color }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Số lượng:</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="decrease-qty" class="w-8 h-8 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="w-16 text-center border border-gray-300 rounded-md">
                                    <button type="button" id="increase-qty" class="w-8 h-8 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50">+</button>
                                </div>
                            </div>

                            <!-- Stock Info -->
                            <div id="stock-info" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="flex space-x-4">
                                <button type="button" id="add-to-cart" 
                                        class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-md font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    Thêm vào giỏ hàng
                                </button>
                                <button type="button" class="px-6 py-3 border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- No variants - simple add to cart -->
                    <div class="space-y-4">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Số lượng:</label>
                            <div class="flex items-center space-x-2">
                                <button type="button" id="decrease-qty" class="w-8 h-8 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50">-</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" class="w-16 text-center border border-gray-300 rounded-md">
                                <button type="button" id="increase-qty" class="w-8 h-8 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50">+</button>
                            </div>
                        </div>

                        <button type="button" id="add-to-cart" 
                                class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                @endif

                <!-- Tags -->
                @if($product->tags->count() > 0)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->tags as $tag)
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Description -->
        @if($product->description)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Mô tả sản phẩm</h2>
                <div class="prose max-w-none">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        @endif

        <!-- Reviews Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Đánh giá sản phẩm</h2>
            
            @if($product->approvedReviews->count() > 0)
                <div class="space-y-6">
                    @foreach($product->approvedReviews as $review)
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center space-x-4 mb-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $review->user->name }}</span>
                                @if($review->is_verified_purchase)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Đã mua hàng</span>
                                @endif
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            @if($review->title)
                                <h4 class="font-medium text-gray-900 mb-1">{{ $review->title }}</h4>
                            @endif
                            
                            @if($review->content)
                                <p class="text-gray-600">{{ $review->content }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đánh giá</h3>
                    <p class="mt-1 text-sm text-gray-500">Hãy là người đầu tiên đánh giá sản phẩm này.</p>
                </div>
            @endif
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm liên quan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                            <!-- Product Image -->
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                                @if($relatedProduct->getFirstMediaUrl('images'))
                                    <img src="{{ $relatedProduct->getFirstMediaUrl('images', 'preview') }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="h-48 w-full object-cover object-center">
                                @else
                                    <div class="h-48 w-full flex items-center justify-center bg-gray-200">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    <a href="{{ route('products.show', $relatedProduct) }}" class="hover:text-indigo-600">
                                        {{ $relatedProduct->name }}
                                    </a>
                                </h3>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        @if($relatedProduct->sale_price)
                                            <span class="text-lg font-bold text-red-600">₫{{ number_format($relatedProduct->sale_price) }}</span>
                                            <span class="text-sm text-gray-500 line-through">₫{{ number_format($relatedProduct->base_price) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">₫{{ number_format($relatedProduct->base_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('products.show', $relatedProduct) }}" 
                                       class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 text-center block">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain">
    </div>
</div>

<script>
// Image gallery variables
let currentImageIndex = 0;
const totalImages = {{ $product->getMedia('images')->count() }};
const imageUrls = @json($product->getMedia('images')->pluck('url'));

// Image gallery
function changeMainImage(imageUrl, index) {
    document.getElementById('main-image').src = imageUrl;
    document.getElementById('current-image').textContent = index;
    currentImageIndex = index - 1;
    
    // Update active thumbnail
    document.querySelectorAll('.aspect-w-1 button').forEach((btn, i) => {
        if (i === currentImageIndex) {
            btn.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-200');
            btn.classList.remove('border-gray-200');
        } else {
            btn.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-200');
            btn.classList.add('border-gray-200');
        }
    });
    
    updateNavigationButtons();
}

function previousImage() {
    if (currentImageIndex > 0) {
        currentImageIndex--;
        const imageUrl = imageUrls[currentImageIndex];
        changeMainImage(imageUrl, currentImageIndex + 1);
    }
}

function nextImage() {
    if (currentImageIndex < totalImages - 1) {
        currentImageIndex++;
        const imageUrl = imageUrls[currentImageIndex];
        changeMainImage(imageUrl, currentImageIndex + 1);
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (prevBtn) prevBtn.disabled = currentImageIndex === 0;
    if (nextBtn) nextBtn.disabled = currentImageIndex === totalImages - 1;
}

// Image modal
function openImageModal(imageUrl) {
    document.getElementById('modal-image').src = imageUrl;
    document.getElementById('image-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal on background click
document.getElementById('image-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('image-modal').classList.contains('hidden')) {
        if (e.key === 'ArrowLeft') {
            previousImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    }
});
        // Product variants data
        const variants = @json($product->variants);
        let selectedSize = null;
        let selectedColor = null;
        let selectedVariant = null;

        // Add to cart functionality
        document.getElementById('add-to-cart').addEventListener('click', function() {
            const quantity = parseInt(document.getElementById('quantity').value);
            
            if (variants.length > 0) {
                // Product has variants
                if (!selectedVariant) {
                    showNotification('Vui lòng chọn kích thước và màu sắc', 'error');
                    return;
                }
                
                addToCart(selectedVariant.id, quantity);
            } else {
                // Product has no variants - use first variant or create default
                if (variants.length === 0) {
                    showNotification('Sản phẩm này hiện không có biến thể nào', 'error');
                    return;
                }
                
                addToCart(variants[0].id, quantity);
            }
        });

        function addToCart(variantId, quantity) {
            const button = document.getElementById('add-to-cart');
            const originalText = button.textContent;
            
            // Show loading state
            button.disabled = true;
            button.textContent = 'Đang thêm...';
            
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateHeaderCartCount(data.cart_count);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                // Reset button state
                button.disabled = false;
                button.textContent = originalText;
            });
        }

        function updateHeaderCartCount(count) {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = count;
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

// Image gallery
function changeMainImage(imageUrl) {
    document.getElementById('main-image').src = imageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.aspect-w-1 button').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-indigo-500');
    });
    event.target.closest('button').classList.add('ring-2', 'ring-indigo-500');
}

// Variant selection
document.querySelectorAll('.size-option').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedSize = this.dataset.size;
        this.classList.add('bg-indigo-600', 'text-white');
        this.classList.remove('border-gray-300');
        
        // Remove selection from other size buttons
        document.querySelectorAll('.size-option').forEach(b => {
            if (b !== this) {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('border-gray-300');
            }
        });
        
        updateVariant();
    });
});

document.querySelectorAll('.color-option').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedColor = this.dataset.color;
        this.classList.add('bg-indigo-600', 'text-white');
        this.classList.remove('border-gray-300');
        
        // Remove selection from other color buttons
        document.querySelectorAll('.color-option').forEach(b => {
            if (b !== this) {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('border-gray-300');
            }
        });
        
        updateVariant();
    });
});

function updateVariant() {
    // Find matching variant
    selectedVariant = variants.find(v => {
        return v.size === selectedSize && v.color === selectedColor;
    });
    
    const stockInfo = document.getElementById('stock-info');
    const addToCartBtn = document.getElementById('add-to-cart');
    
    if (selectedVariant) {
        stockInfo.innerHTML = `Còn lại: ${selectedVariant.stock} sản phẩm`;
        addToCartBtn.disabled = selectedVariant.stock <= 0;
        addToCartBtn.textContent = selectedVariant.stock <= 0 ? 'Hết hàng' : 'Thêm vào giỏ hàng';
    } else {
        stockInfo.innerHTML = 'Vui lòng chọn kích thước và màu sắc';
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Chọn biến thể';
    }
}

// Quantity controls
document.getElementById('increase-qty').addEventListener('click', function() {
    const qtyInput = document.getElementById('quantity');
    qtyInput.value = parseInt(qtyInput.value) + 1;
});

document.getElementById('decrease-qty').addEventListener('click', function() {
    const qtyInput = document.getElementById('quantity');
    if (parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
    }
});

// Add to cart
document.getElementById('add-to-cart').addEventListener('click', function() {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    if (!selectedVariant && variants.length > 0) {
        alert('Vui lòng chọn kích thước và màu sắc');
        return;
    }
    
    if (selectedVariant && selectedVariant.stock < quantity) {
        alert('Không đủ hàng trong kho');
        return;
    }
    
    // TODO: Implement actual cart functionality
    alert('Đã thêm vào giỏ hàng! (Chức năng sẽ được triển khai trong Sprint 3)');
});

// Initialize
if (variants.length > 0) {
    updateVariant();
}
</script>
@endsection
