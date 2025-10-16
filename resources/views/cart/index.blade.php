@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Giỏ hàng</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Giỏ hàng của bạn</h1>

        @if($cart->items->count() > 0)
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
                <!-- Cart Items -->
                <section class="lg:col-span-7">
                    <div class="flow-root">
                        <ul role="list" class="-my-6 divide-y divide-gray-200">
                            @foreach($cart->items as $item)
                                <li class="flex py-6" data-cart-item="{{ $item->id }}">
                                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                        @if($item->variant->product->getFirstMediaUrl('images'))
                                            <img src="{{ $item->variant->product->getFirstMediaUrl('images', 'thumb') }}"
                                                 alt="{{ $item->variant->product->name }}"
                                                 class="h-full w-full object-cover object-center">
                                        @else
                                            <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex flex-1 flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>
                                                    <a href="{{ route('products.show', $item->variant->product->slug) }}" class="hover:text-indigo-600">
                                                        {{ $item->variant->product->name }}
                                                    </a>
                                                </h3>
                                                <p class="ml-4 item-subtotal">{{ number_format($item->subtotal, 0, ',', '.') }}đ</p>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                <p>SKU: {{ $item->variant->sku }}</p>
                                                @if($item->variant->size)
                                                    <p>Kích thước: {{ $item->variant->size }}</p>
                                                @endif
                                                @if($item->variant->color)
                                                    <p>Màu sắc: {{ $item->variant->color }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-1 items-end justify-between text-sm">
                                            <div class="flex items-center space-x-2">
                                                <label for="quantity-{{ $item->id }}" class="text-gray-500">Số lượng:</label>
                                                <select id="quantity-{{ $item->id }}" 
                                                        class="quantity-select border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                        data-item-id="{{ $item->id }}"
                                                        data-max="{{ $item->variant->stock }}">
                                                    @for($i = 1; $i <= min(99, $item->variant->stock); $i++)
                                                        <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="flex">
                                                <button type="button" 
                                                        class="font-medium text-red-600 hover:text-red-500 remove-item"
                                                        data-item-id="{{ $item->id }}">
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>

                <!-- Order Summary -->
                <section class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:col-span-5 lg:mt-0 lg:p-8">
                    <h2 class="text-lg font-medium text-gray-900">Tóm tắt đơn hàng</h2>

                    <dl class="mt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Tạm tính</dt>
                            <dd class="text-sm font-medium text-gray-900 cart-subtotal">{{ number_format($cart->total, 0, ',', '.') }}đ</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Phí vận chuyển</dt>
                            <dd class="text-sm font-medium text-gray-900">Miễn phí</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <dt class="text-base font-medium text-gray-900">Tổng cộng</dt>
                            <dd class="text-base font-medium text-gray-900 cart-total">{{ number_format($cart->total, 0, ',', '.') }}đ</dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="{{ route('checkout.index') }}" 
                           class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex justify-center items-center">
                            Thanh toán
                        </a>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </section>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-12">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Giỏ hàng trống</h3>
                <p class="mt-1 text-sm text-gray-500">Bắt đầu mua sắm để thêm sản phẩm vào giỏ hàng.</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Mua sắm ngay
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded');
    
    // Update quantity
    const quantitySelects = document.querySelectorAll('.quantity-select');
    console.log('Found quantity selects:', quantitySelects.length);
    
    quantitySelects.forEach(select => {
        select.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const quantity = this.value;
            
            updateCartItem(itemId, quantity);
        });
    });

    // Remove item
    const removeButtons = document.querySelectorAll('.remove-item');
    console.log('Found remove buttons:', removeButtons.length);
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                removeCartItem(itemId);
            }
        });
    });

    function updateCartItem(itemId, quantity) {
        console.log('Updating cart item:', itemId, 'quantity:', quantity);
        
        fetch(`/cart/items/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update subtotal
                const itemElement = document.querySelector(`[data-cart-item="${itemId}"]`);
                const subtotalElement = itemElement.querySelector('.item-subtotal');
                subtotalElement.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
                
                // Update cart totals
                document.querySelector('.cart-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + 'đ';
                document.querySelector('.cart-total').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + 'đ';
                
                // Update header cart count
                updateHeaderCartCount(data.cart_count);
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        });
    }

    function removeCartItem(itemId) {
        console.log('Removing cart item:', itemId);
        
        fetch(`/cart/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                // Remove item from DOM
                const itemElement = document.querySelector(`[data-cart-item="${itemId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }
                
                // Update cart totals
                document.querySelector('.cart-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + 'đ';
                document.querySelector('.cart-total').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + 'đ';
                
                // Update header cart count
                updateHeaderCartCount(data.cart_count);
                
                // Check if cart is empty
                if (data.cart_count === 0) {
                    location.reload(); // Reload to show empty cart message
                }
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        });
    }

    function updateHeaderCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection
