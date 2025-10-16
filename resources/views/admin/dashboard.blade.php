@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Dashboard Quản Trị</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Stats Cards -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Tổng đơn hàng</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
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
                                <p class="text-2xl font-semibold text-gray-900">0 ₫</p>
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
                                <p class="text-sm font-medium text-gray-600">Sản phẩm</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
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
                                <p class="text-sm font-medium text-gray-600">Khách hàng</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Thao tác nhanh</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="#" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý sản phẩm</h3>
                            <p class="text-sm text-gray-600">Thêm, sửa, xóa sản phẩm</p>
                        </a>
                        <a href="#" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý đơn hàng</h3>
                            <p class="text-sm text-gray-600">Xem và xử lý đơn hàng</p>
                        </a>
                        <a href="#" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900">Quản lý người dùng</h3>
                            <p class="text-sm text-gray-600">Xem danh sách khách hàng</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

