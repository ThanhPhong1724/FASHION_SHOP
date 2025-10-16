@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt hàng thành công!</h1>
            <p class="text-lg text-gray-600">Cảm ơn bạn đã mua sắm tại Fashion Shop</p>
        </div>

        <!-- Order Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin đơn hàng</h2>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Mã đơn hàng</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ngày đặt hàng</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->status_label }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phương thức thanh toán</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->payment_method_label }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tổng tiền</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($order->total, 0, ',', '.') }}đ</dd>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Địa chỉ giao hàng</h2>
            
            <div class="text-sm text-gray-900">
                <div class="font-medium">{{ $order->shippingAddress->name }}</div>
                <div>{{ $order->shippingAddress->phone }}</div>
                <div>{{ $order->shippingAddress->full_address }}</div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Sản phẩm đã đặt</h2>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                            @if($item->variant->product->getFirstMediaUrl('images'))
                                <img src="{{ $item->variant->product->getFirstMediaUrl('images', 'thumb') }}"
                                     alt="{{ $item->variant->product->name }}"
                                     class="h-full w-full object-cover object-center">
                            @else
                                <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $item->variant_details_string }}</p>
                            <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ number_format($item->subtotal, 0, ',', '.') }}đ
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium text-blue-900 mb-4">Bước tiếp theo</h2>
            <div class="text-sm text-blue-800 space-y-2">
                <p>• Chúng tôi sẽ xác nhận đơn hàng trong vòng 24 giờ</p>
                <p>• Bạn sẽ nhận được email xác nhận đơn hàng</p>
                <p>• Đơn hàng sẽ được giao trong 2-5 ngày làm việc</p>
                <p>• Bạn có thể theo dõi trạng thái đơn hàng trong tài khoản của mình</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Tiếp tục mua sắm
            </a>
            
            @if(auth()->check())
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Xem đơn hàng của tôi
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
