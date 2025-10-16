<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Fashion Shop'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                                Fashion Shop
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Trang chủ
                            </a>
                            <a href="{{ route('products.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Sản phẩm
                            </a>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 flex items-center justify-center px-2 lg:ml-6 lg:justify-end">
                        <div class="max-w-lg w-full lg:max-w-xs">
                            <form action="{{ route('products.index') }}" method="GET">
                                <label for="search" class="sr-only">Tìm kiếm</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Tìm kiếm sản phẩm..." type="search" value="{{ request('search') }}">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        @auth
                            <!-- Wishlist -->
                            <a href="{{ route('wishlist.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center wishlist-count">{{ auth()->user()->wishlists()->count() }}</span>
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                </svg>
                                <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </a>

                            <!-- Profile dropdown -->
                            <div class="ml-3 relative">
                                <div class="flex items-center">
                                    <span class="text-gray-700 text-sm font-medium mr-2">{{ Auth::user()->name }}</span>
                                    <a href="{{ route('profile.edit') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                        Tài khoản
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                                        @csrf
                                        <button type="submit" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-4">
                                <!-- Cart for guests -->
                                <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                    </svg>
                                    <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                                </a>
                                
                                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Đăng ký
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Fashion Shop</h3>
                        <p class="text-gray-300">Cửa hàng thời trang hàng đầu với những sản phẩm chất lượng cao và giá cả hợp lý.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Danh mục</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('products.index') }}" class="text-gray-300 hover:text-white">Tất cả sản phẩm</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Áo thun</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Quần jean</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Giày dép</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Hỗ trợ</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white">Liên hệ</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Hướng dẫn mua hàng</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Chính sách đổi trả</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Bảo mật thông tin</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liên hệ</h3>
                        <ul class="space-y-2 text-gray-300">
                            <li>📧 info@fashionshop.com</li>
                            <li>📞 0123 456 789</li>
                            <li>📍 123 Đường ABC, Quận 1, TP.HCM</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                    <p>&copy; {{ date('Y') }} Fashion Shop. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <script>
    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = data.count;
                });
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
    });
    </script>
</body>
</html>