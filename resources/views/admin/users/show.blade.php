@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Chi tiết người dùng: {{ $user->name }}</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                            Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Quay lại
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- User Information -->
                    <div class="lg:col-span-2">
                        <!-- Basic Info -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ngày đăng ký</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email đã xác thực</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Đã xác thực</span>
                                        @else
                                            <span class="text-red-600">✗ Chưa xác thực</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->is_active ?? true)
                                            <span class="text-green-600">Hoạt động</span>
                                        @else
                                            <span class="text-red-600">Vô hiệu hóa</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Đơn hàng gần đây</h3>
                            @if($user->orders->count() > 0)
                                <div class="space-y-3">
                                    @foreach($user->orders->take(5) as $order)
                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                                <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
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
                                    @endforeach
                                </div>
                                @if($user->orders->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('admin.orders.index', ['search' => $user->email]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Xem tất cả {{ $user->orders->count() }} đơn hàng
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-500 text-center py-4">Chưa có đơn hàng nào</p>
                            @endif
                        </div>

                        <!-- Recent Reviews -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Đánh giá gần đây</h3>
                            @if($user->reviews->count() > 0)
                                <div class="space-y-3">
                                    @foreach($user->reviews->take(5) as $review)
                                        <div class="p-3 bg-white rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900">{{ $review->product->name }}</h4>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ Str::limit($review->content, 100) }}</p>
                                            <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Chưa có đánh giá nào</p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Roles -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Vai trò</h3>
                            <div class="space-y-2">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($role->name === 'admin') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                                @if($user->roles->count() === 0)
                                    <p class="text-gray-500 text-sm">Chưa có vai trò</p>
                                @endif
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thống kê</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tổng đơn hàng:</span>
                                    <span class="font-medium">{{ $user->orders->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tổng đánh giá:</span>
                                    <span class="font-medium">{{ $user->reviews->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Địa chỉ:</span>
                                    <span class="font-medium">{{ $user->addresses->count() }}</span>
                                </div>
                                @if($user->orders->count() > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tổng chi tiêu:</span>
                                        <span class="font-medium">{{ number_format($user->orders->sum('total'), 0, ',', '.') }} ₫</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Addresses -->
                        @if($user->addresses->count() > 0)
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Địa chỉ</h3>
                                <div class="space-y-3">
                                    @foreach($user->addresses->take(3) as $address)
                                        <div class="p-3 bg-white rounded-lg">
                                            <p class="font-medium text-gray-900">{{ $address->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->full_address }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
