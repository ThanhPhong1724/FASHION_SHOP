@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Quản lý đơn hàng</h1>
                    <a href="{{ route('admin.orders.export') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Xuất CSV
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Mã đơn hàng, tên khách hàng..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Lọc
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mã đơn hàng
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Khách hàng
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tổng tiền
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thanh toán
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày tạo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($order->user)
                                                {{ $order->user->name }}
                                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                            @else
                                                Guest
                                                <div class="text-sm text-gray-500">{{ $order->guest_email }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($order->total, 0, ',', '.') }} ₫
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->payment_method_label }}</div>
                                        <div class="text-sm text-gray-500">{{ ucfirst($order->payment_status) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Xem</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Không có đơn hàng nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

