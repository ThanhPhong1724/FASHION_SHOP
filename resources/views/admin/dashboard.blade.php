@extends('admin.layout')

@section('content')
<div class="py-12">
    <div style="min-width: 1736px;" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Dashboard Quản Trị</h1>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Đơn hàng hôm nay</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $todayOrders }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Doanh thu hôm nay</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($todayRevenue, 0, ',', '.') }} ₫</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Tổng sản phẩm</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalProducts }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Tổng khách hàng</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Thống kê tổng quan</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Danh mục:</span>
                                <span class="font-medium">{{ $totalCategories }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Thương hiệu:</span>
                                <span class="font-medium">{{ $totalBrands }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Đơn hàng theo trạng thái</h3>
                        <div class="space-y-2">
                            @foreach($ordersByStatus as $status => $count)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ ucfirst($status) }}:</span>
                                    <span class="font-medium">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Orders & Top Products -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Recent Orders -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Đơn hàng gần đây</h3>
                        <div class="space-y-3">
                            @forelse($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-600">
                                            @if($order->user)
                                                {{ $order->user->name }}
                                            @else
                                                {{ $order->guest_email ?? 'Guest' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">{{ number_format($order->total, 0, ',', '.') }} ₫</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Chưa có đơn hàng nào</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Sản phẩm bán chạy</h3>
                        <div class="space-y-3">
                            @forelse($topProducts as $product)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    @if($product->getFirstMediaUrl('images'))
                                        <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">{{ $product->sales_count }}</p>
                                        <p class="text-sm text-gray-600">đã bán</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Chưa có sản phẩm nào</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Thao tác nhanh</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.products.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý sản phẩm</h3>
                            <p class="text-sm text-gray-600">Thêm, sửa, xóa sản phẩm</p>
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý đơn hàng</h3>
                            <p class="text-sm text-gray-600">Xem và xử lý đơn hàng</p>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý danh mục</h3>
                            <p class="text-sm text-gray-600">Thêm, sửa danh mục</p>
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý thương hiệu</h3>
                            <p class="text-sm text-gray-600">Thêm, sửa thương hiệu</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

