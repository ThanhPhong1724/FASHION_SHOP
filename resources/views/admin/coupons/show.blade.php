@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Chi tiết mã giảm giá: {{ $coupon->code }}</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                           class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                            Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.coupons.index') }}" 
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
                    <!-- Coupon Information -->
                    <div class="lg:col-span-2">
                        <!-- Basic Info -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mã giảm giá</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $coupon->code }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $coupon->name }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $coupon->description ?? 'Không có mô tả' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Configuration -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Cấu hình giảm giá</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loại</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($coupon->type === 'percentage') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $coupon->type_label }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($coupon->type === 'percentage')
                                            {{ $coupon->value }}%
                                            @if($coupon->max_discount)
                                                <br><span class="text-xs text-gray-500">Tối đa: {{ number_format($coupon->max_discount, 0, ',', '.') }} ₫</span>
                                            @endif
                                        @else
                                            {{ number_format($coupon->value, 0, ',', '.') }} ₫
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đơn hàng tối thiểu</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($coupon->min_order_amount > 0)
                                            {{ number_format($coupon->min_order_amount, 0, ',', '.') }} ₫
                                        @else
                                            Không có điều kiện
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($coupon->status_color === 'green') bg-green-100 text-green-800
                                            @elseif($coupon->status_color === 'red') bg-red-100 text-red-800
                                            @elseif($coupon->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                            @elseif($coupon->status_color === 'orange') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $coupon->status_label }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Usage History -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Lịch sử sử dụng</h3>
                            @if($coupon->users->count() > 0)
                                <div class="space-y-3">
                                    @foreach($coupon->users->take(10) as $user)
                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    @if($user->pivot->user_id)
                                                        {{ $user->name }} ({{ $user->email }})
                                                    @else
                                                        Guest User
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    @if($user->pivot->order_id)
                                                        Đơn hàng: #{{ $user->pivot->order_id }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-500">{{ $user->pivot->used_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($coupon->users->count() > 10)
                                    <div class="mt-4 text-center">
                                        <p class="text-sm text-gray-500">Hiển thị 10 lượt sử dụng gần nhất trong tổng số {{ $coupon->users->count() }} lượt</p>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-500 text-center py-4">Chưa có lượt sử dụng nào</p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Statistics -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thống kê</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tổng lượt sử dụng:</span>
                                    <span class="font-medium">{{ $coupon->used_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Giới hạn:</span>
                                    <span class="font-medium">{{ $coupon->usage_limit ?? 'Không giới hạn' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Còn lại:</span>
                                    <span class="font-medium">
                                        @if($coupon->usage_limit)
                                            {{ max(0, $coupon->usage_limit - $coupon->used_count) }}
                                        @else
                                            Không giới hạn
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Người dùng:</span>
                                    <span class="font-medium">{{ $coupon->users->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Time Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thời gian</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ngày tạo</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $coupon->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @if($coupon->starts_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bắt đầu</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $coupon->starts_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($coupon->expires_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Hết hạn</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $coupon->expires_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Thao tác</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                   class="block w-full text-center bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                                    Chỉnh sửa
                                </a>
                                <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="block w-full text-center {{ $coupon->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-md">
                                        {{ $coupon->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                                    </button>
                                </form>
                                @if($coupon->used_count === 0)
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

