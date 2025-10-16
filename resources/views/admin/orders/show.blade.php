@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Chi tiết đơn hàng: {{ $order->order_number }}</h1>
                    <a href="{{ route('admin.orders.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Quay lại
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Information -->
                    <div class="lg:col-span-2">
                        <!-- Order Items -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Sản phẩm đã đặt</h3>
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex items-center space-x-4 p-4 bg-white rounded-lg">
                                        @if($item->variant->product->getFirstMediaUrl('images'))
                                            <img src="{{ $item->variant->product->getFirstMediaUrl('images', 'thumb') }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                                            <p class="text-sm text-gray-600">
                                                @if($item->variant_details['size']) Size: {{ $item->variant_details['size'] }} @endif
                                                @if($item->variant_details['color']) - Màu: {{ $item->variant_details['color'] }} @endif
                                            </p>
                                            <p class="text-sm text-gray-600">SKU: {{ $item->variant_details['sku'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">{{ $item->quantity }}x</p>
                                            <p class="text-sm text-gray-600">{{ number_format($item->unit_price, 0, ',', '.') }} ₫</p>
                                            <p class="font-medium text-gray-900">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        @if($order->shippingAddress)
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Địa chỉ giao hàng</h3>
                                <div class="bg-white p-4 rounded-lg">
                                    <p class="font-medium text-gray-900">{{ $order->shippingAddress->name }}</p>
                                    <p class="text-gray-600">{{ $order->shippingAddress->phone }}</p>
                                    <p class="text-gray-600">{{ $order->shippingAddress->address_line1 }}</p>
                                    @if($order->shippingAddress->address_line2)
                                        <p class="text-gray-600">{{ $order->shippingAddress->address_line2 }}</p>
                                    @endif
                                    <p class="text-gray-600">
                                        {{ $order->shippingAddress->ward }}, 
                                        {{ $order->shippingAddress->district }}, 
                                        {{ $order->shippingAddress->city }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Order Summary & Actions -->
                    <div class="lg:col-span-1">
                        <!-- Order Status Update -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Cập nhật trạng thái</h3>
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái đơn hàng</label>
                                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái thanh toán</label>
                                    <select name="payment_status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Cập nhật
                                </button>
                            </form>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Tóm tắt đơn hàng</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Mã đơn hàng:</span>
                                    <span class="font-medium">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ngày tạo:</span>
                                    <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Khách hàng:</span>
                                    <span class="font-medium">
                                        @if($order->user)
                                            {{ $order->user->name }}
                                        @else
                                            Guest ({{ $order->guest_email }})
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phương thức thanh toán:</span>
                                    <span class="font-medium">{{ $order->payment_method_label }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tạm tính:</span>
                                    <span class="font-medium">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phí vận chuyển:</span>
                                    <span class="font-medium">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                                </div>
                                @if($order->discount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Giảm giá:</span>
                                        <span class="font-medium text-red-600">-{{ number_format($order->discount, 0, ',', '.') }} ₫</span>
                                    </div>
                                @endif
                                <div class="flex justify-between border-t pt-3">
                                    <span class="text-lg font-semibold">Tổng cộng:</span>
                                    <span class="text-lg font-semibold">{{ number_format($order->total, 0, ',', '.') }} ₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

