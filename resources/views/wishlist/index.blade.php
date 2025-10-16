@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8" style="min-width: 1600px;">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Danh sách yêu thích</h1>
        <p class="text-gray-600 mt-2">Các sản phẩm bạn đã lưu để xem sau</p>
    </div>

    @if($wishlistItems->count() > 0)
        <!-- Wishlist Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlistItems as $product)
                @if($product) {{-- Kiểm tra product tồn tại --}}
                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow group" 
                     data-product-id="{{ $product->id }}" 
                     data-product-slug="{{ $product->slug }}">
                    <!-- Product Image -->
                    <div class="relative aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                        @if($product->getFirstMediaUrl('images'))
                            <img src="{{ $product->getFirstMediaUrl('images', 'preview') }}" 
                                 alt="{{ $product->name }}" 
                                 class="h-48 w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="h-48 w-full flex items-center justify-center bg-gray-200">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Remove from wishlist button -->
                        <button type="button" 
                                onclick="removeFromWishlist({{ $product->id }})"
                                class="absolute top-2 right-2 bg-white bg-opacity-80 hover:bg-opacity-100 text-red-500 rounded-full p-2 transition-all duration-200 opacity-0 group-hover:opacity-100">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2 line-clamp-2">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        @if($product->category)
                            <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                @if($product->sale_price && $product->sale_price < $product->base_price)
                                    <span class="text-lg font-bold text-red-600">₫{{ number_format($product->sale_price) }}</span>
                                    <span class="text-sm text-gray-500 line-through">₫{{ number_format($product->base_price) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">₫{{ number_format($product->base_price) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 text-center transition-colors">
                                Xem chi tiết
                            </a>
                            <button type="button" 
                                    onclick="addToCartFromWishlist({{ $product->id }})"
                                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $wishlistItems->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Danh sách yêu thích trống</h3>
            <p class="mt-2 text-gray-500">Bạn chưa có sản phẩm nào trong danh sách yêu thích.</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Khám phá sản phẩm
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Remove from wishlist form (hidden) -->
<form id="remove-wishlist-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Remove from wishlist function
function removeFromWishlist(productId) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
        fetch(`/wishlist/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateWishlistCount(data.wishlist_count);
                // Remove product card from DOM
                const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                if (productCard) {
                    productCard.remove();
                }
                // Check if wishlist is empty
                const remainingItems = document.querySelectorAll('[data-product-id]');
                if (remainingItems.length === 0) {
                    location.reload(); // Reload to show empty state
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        });
    }
}

// Add to cart from wishlist
function addToCartFromWishlist(productId) {
    // Lấy slug từ data attribute
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        const productSlug = productCard.dataset.productSlug;
        if (productSlug) {
            window.location.href = `/products/${productSlug}`;
        } else {
            showNotification('Không thể tìm thấy sản phẩm', 'error');
        }
    } else {
        showNotification('Không thể tìm thấy sản phẩm', 'error');
    }
}

// Add to cart function
function addToCart(variantId, quantity) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            variant_id: variantId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateHeaderCartCount(data.cart_count);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
    });
}

// Update header cart count
function updateHeaderCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count;
    });
}

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Update wishlist count in header
function updateWishlistCount(count) {
    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
    wishlistCountElements.forEach(element => {
        element.textContent = count;
    });
}
</script>
@endsection
