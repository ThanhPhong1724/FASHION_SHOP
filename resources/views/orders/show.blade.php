@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Đơn hàng #{{ $order->order_number }}</h1>
                    <p class="mt-2 text-gray-600">Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status === 'shipping') bg-purple-100 text-purple-800
                        @elseif($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @switch($order->status)
                            @case('pending') Chờ xử lý @break
                            @case('processing') Đang xử lý @break
                            @case('shipping') Đang giao @break
                            @case('completed') Hoàn thành @break
                            @case('cancelled') Đã hủy @break
                            @default {{ ucfirst($order->status) }} @break
                        @endswitch
                    </span>
                    
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Hủy đơn hàng
                            </button>
                        </form>
                    @endif
                    
                    @if($order->status === 'completed')
                        <form method="POST" action="{{ route('orders.reorder', $order) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Đặt lại
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Sản phẩm đã đặt</h3>
                        
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if($item->variant && $item->variant->product && $item->variant->product->getFirstMediaUrl('images'))
                                            <img src="{{ $item->variant->product->getFirstMediaUrl('images', 'preview') }}" 
                                                 alt="{{ $item->product_name }}"
                                                 class="h-16 w-16 rounded-lg object-cover">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $item->product_name }}
                                        </h4>
                                        @if($item->variant)
                                            <p class="text-sm text-gray-500">
                                                @if($item->variant->size) Size: {{ $item->variant->size }} @endif
                                                @if($item->variant->color) • Màu: {{ $item->variant->color }} @endif
                                            </p>
                                        @endif
                                        <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->unit_price, 0, ',', '.') }} ₫
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Tổng: {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tóm tắt đơn hàng</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span class="font-medium">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                            </div>
                            
                            @if($order->discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Giảm giá:</span>
                                    <span class="font-medium text-green-600">-{{ number_format($order->discount, 0, ',', '.') }} ₫</span>
                                </div>
                            @endif
                            
                            @if($order->shipping_fee > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Phí vận chuyển:</span>
                                    <span class="font-medium">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                                </div>
                            @endif
                            
                            @if($order->tax > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Thuế:</span>
                                    <span class="font-medium">{{ number_format($order->tax, 0, ',', '.') }} ₫</span>
                                </div>
                            @endif
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-base font-medium text-gray-900">Tổng cộng:</span>
                                    <span class="text-base font-bold text-gray-900">{{ number_format($order->total, 0, ',', '.') }} ₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applied Coupons -->
                @if($order->coupons->count() > 0)
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Mã giảm giá đã áp dụng</h3>
                            
                            <div class="space-y-2">
                                @foreach($order->coupons as $coupon)
                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-green-800">{{ $coupon->code }}</p>
                                            <p class="text-xs text-green-600">
                                                @if($coupon->type === 'percentage')
                                                    Giảm {{ $coupon->value }}%
                                                @else
                                                    Giảm {{ number_format($coupon->value, 0, ',', '.') }} ₫
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-sm font-medium text-green-800">
                                            -{{ number_format($coupon->pivot->discount_amount ?? 0, 0, ',', '.') }} ₫
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Shipping Address -->
                @if($order->shippingAddress)
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Địa chỉ giao hàng</h3>
                            
                            <div class="text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ $order->shippingAddress->name }}</p>
                                <p>{{ $order->shippingAddress->phone }}</p>
                                <p>{{ $order->shippingAddress->address_line1 }}</p>
                                @if($order->shippingAddress->address_line2)
                                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                                @endif
                                <p>{{ $order->shippingAddress->ward }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->city }}</p>
                                @if($order->shippingAddress->postal_code)
                                    <p>{{ $order->shippingAddress->postal_code }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Information -->
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Thông tin thanh toán</h3>
                        
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between mb-2">
                                <span>Phương thức:</span>
                                <span class="font-medium">
                                    @switch($order->payment_method)
                                        @case('cod') Thanh toán khi nhận hàng @break
                                        @case('vnpay') VNPay @break
                                        @case('momo') MoMo @break
                                        @default {{ ucfirst($order->payment_method) }} @break
                                    @endswitch
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Trạng thái:</span>
                                <span class="font-medium">
                                    @switch($order->payment_status)
                                        @case('pending') Chờ thanh toán @break
                                        @case('paid') Đã thanh toán @break
                                        @case('failed') Thanh toán thất bại @break
                                        @case('refunded') Đã hoàn tiền @break
                                        @default {{ ucfirst($order->payment_status) }} @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('orders.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại danh sách đơn hàng
            </a>
        </div>
    </div>
</div>
@endsection
