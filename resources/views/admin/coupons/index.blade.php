@extends('admin.layout')

@section('content')
<div class="py-12">
    <div style="min-width: 1736px;" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Quản lý mã giảm giá</h1>
                    <a href="{{ route('admin.coupons.create') }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Tạo mã giảm giá mới
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Mã, tên..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                            <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Lọc
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Coupons Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mã giảm giá
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Loại
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Giá trị
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Điều kiện
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sử dụng
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($coupons as $coupon)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $coupon->code }}</div>
                                            <div class="text-sm text-gray-500">{{ $coupon->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($coupon->type === 'percentage') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $coupon->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($coupon->type === 'percentage')
                                            {{ $coupon->value }}%
                                            @if($coupon->max_discount)
                                                <br><span class="text-xs text-gray-500">Tối đa: {{ number_format($coupon->max_discount, 0, ',', '.') }} ₫</span>
                                            @endif
                                        @else
                                            {{ number_format($coupon->value, 0, ',', '.') }} ₫
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($coupon->min_order_amount > 0)
                                            Đơn tối thiểu: {{ number_format($coupon->min_order_amount, 0, ',', '.') }} ₫
                                        @else
                                            Không có điều kiện
                                        @endif
                                        @if($coupon->expires_at)
                                            <br><span class="text-xs">Hết hạn: {{ $coupon->expires_at->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $coupon->used_count }}/{{ $coupon->usage_limit ?? '∞' }}
                                        <br><span class="text-xs text-gray-500">{{ $coupon->users_count }} người dùng</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($coupon->status_color === 'green') bg-green-100 text-green-800
                                            @elseif($coupon->status_color === 'red') bg-red-100 text-red-800
                                            @elseif($coupon->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                            @elseif($coupon->status_color === 'orange') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $coupon->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Xem</a>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">Sửa</a>
                                            <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-{{ $coupon->is_active ? 'red' : 'green' }}-600 hover:text-{{ $coupon->is_active ? 'red' : 'green' }}-900">
                                                    {{ $coupon->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                                                </button>
                                            </form>
                                            @if($coupon->used_count === 0)
                                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" 
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Không có mã giảm giá nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

