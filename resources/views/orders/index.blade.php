@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Lịch sử đơn hàng</h1>
            <p class="mt-2 text-gray-600">Theo dõi và quản lý các đơn hàng của bạn</p>
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

        <!-- Orders List -->
        @if($orders->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Đơn hàng #{{ $order->order_number }}
                                                </p>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                            </div>
                                            <div class="mt-1">
                                                <p class="text-sm text-gray-500">
                                                    {{ $order->created_at->format('d/m/Y H:i') }} • 
                                                    {{ $order->items->count() }} sản phẩm • 
                                                    {{ number_format($order->total, 0, ',', '.') }} ₫
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Xem chi tiết
                                        </a>
                                        
                                        @if($order->status === 'pending')
                                            <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"
                                                        class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    Hủy đơn hàng
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($order->status === 'completed')
                                            <form method="POST" action="{{ route('orders.reorder', $order) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm leading-4 font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Đặt lại
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Order Items Preview -->
                                <div class="mt-4">
                                    <div class="flex -space-x-2">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="h-8 w-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                                @if($item->variant && $item->variant->product && $item->variant->product->getFirstMediaUrl('images'))
                                                    <img src="{{ $item->variant->product->getFirstMediaUrl('images', 'preview') }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="h-8 w-8 rounded-full object-cover">
                                                @else
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="h-8 w-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center">
                                                <span class="text-xs text-gray-600">+{{ $order->items->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đơn hàng</h3>
                <p class="mt-1 text-sm text-gray-500">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm!</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Mua sắm ngay
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
