@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . $query)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    @if($query)
                        <h1 class="text-3xl font-bold text-gray-900">
                            Kết quả tìm kiếm cho: "{{ $query }}"
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Tìm thấy {{ $products->total() }} sản phẩm
                        </p>
                    @else
                        <h1 class="text-3xl font-bold text-gray-900">Tìm kiếm sản phẩm</h1>
                        <p class="mt-2 text-gray-600">Nhập từ khóa để tìm kiếm sản phẩm</p>
                    @endif
                </div>
                
                <!-- Search Form -->
                <div class="w-full max-w-md">
                    <form method="GET" action="{{ route('search.index') }}" class="relative">
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}"
                               placeholder="Tìm kiếm sản phẩm..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($query)
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Bộ lọc</h3>
                        
                        <form method="GET" action="{{ route('search.index') }}" class="space-y-6">
                            <input type="hidden" name="q" value="{{ $query }}">
                            
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                                <select name="category" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Brand Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                                <select name="brand" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tất cả thương hiệu</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" 
                                           name="min_price" 
                                           value="{{ request('min_price') }}"
                                           placeholder="Từ"
                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <input type="number" 
                                           name="max_price" 
                                           value="{{ request('max_price') }}"
                                           placeholder="Đến"
                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                                <select name="sort" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Liên quan nhất</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Áp dụng
                                </button>
                                <a href="{{ route('search.index', ['q' => $query]) }}" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                                    Xóa bộ lọc
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    @if($products->count() > 0)
                        <!-- Results Header -->
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-sm text-gray-600">
                                Hiển thị {{ $products->firstItem() }}-{{ $products->lastItem() }} 
                                trong {{ $products->total() }} kết quả
                            </p>
                        </div>

                        <!-- Products Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-t-lg overflow-hidden">
                                        @if($product->getFirstMediaUrl('images'))
                                            <img src="{{ $product->getFirstMediaUrl('images', 'preview') }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-48 object-cover hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="p-4">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2 line-clamp-2">
                                            <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                                                {{ $product->name }}
                                            </a>
                                        </h3>
                                        
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ $product->short_description }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                @if($product->sale_price && $product->sale_price < $product->base_price)
                                                    <span class="text-lg font-bold text-red-600">
                                                        {{ number_format($product->sale_price, 0, ',', '.') }} ₫
                                                    </span>
                                                    <span class="text-sm text-gray-500 line-through">
                                                        {{ number_format($product->base_price, 0, ',', '.') }} ₫
                                                    </span>
                                                @else
                                                    <span class="text-lg font-bold text-gray-900">
                                                        {{ number_format($product->base_price, 0, ',', '.') }} ₫
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($product->approved_reviews_count > 0)
                                                <div class="flex items-center">
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="h-4 w-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="ml-1 text-sm text-gray-600">({{ $product->approved_reviews_count }})</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span>{{ $product->category->name }}</span>
                                            <span>{{ $product->brand->name }}</span>
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
                        <!-- No Results -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy sản phẩm</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Không có sản phẩm nào phù hợp với từ khóa "{{ $query }}"
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('products.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Xem tất cả sản phẩm
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Search Suggestions -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tìm kiếm sản phẩm</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Nhập từ khóa vào ô tìm kiếm để bắt đầu
                </p>
                
                <!-- Popular Searches -->
                <div class="mt-8">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Tìm kiếm phổ biến</h4>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('search.index', ['q' => 'áo thun nam']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200">
                            áo thun nam
                        </a>
                        <a href="{{ route('search.index', ['q' => 'quần jean nữ']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200">
                            quần jean nữ
                        </a>
                        <a href="{{ route('search.index', ['q' => 'giày sneaker']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200">
                            giày sneaker
                        </a>
                        <a href="{{ route('search.index', ['q' => 'túi xách']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200">
                            túi xách
                        </a>
                        <a href="{{ route('search.index', ['q' => 'đồng hồ']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200">
                            đồng hồ
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
