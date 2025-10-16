@extends('layouts.app')

@section('title', 'Thanh toán')

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
                        <a href="{{ route('cart.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Giỏ hàng</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Thanh toán</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Thanh toán</h1>

        <form id="checkout-form" class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
            @csrf
            
            <!-- Checkout Form -->
            <div class="lg:col-span-7">
                <!-- Guest Information -->
                @guest
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin liên lạc</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="guest_email" name="guest_email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="your@email.com">
                        </div>
                        <div>
                            <label for="guest_phone" class="block text-sm font-medium text-gray-700">Số điện thoại *</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="tel" id="guest_phone" name="guest_phone" required
                                       class="flex-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="0123456789">
                                <button type="button" id="send-otp-btn" 
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Gửi OTP
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="otp-section" class="mt-4 hidden">
                        <label for="otp_code" class="block text-sm font-medium text-gray-700">Mã OTP *</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="otp_code" name="otp_code" maxlength="6"
                                   class="flex-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Nhập mã OTP">
                            <button type="button" id="verify-otp-btn" 
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-indigo-600 text-white text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Xác thực
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Mã OTP đã được gửi đến số điện thoại của bạn</p>
                    </div>
                </div>
                @endguest

                <!-- Shipping Address -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Địa chỉ giao hàng</h2>
                    
                    @if(auth()->check() && $addresses->count() > 0)
                        <!-- Saved Addresses -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn địa chỉ có sẵn:</label>
                            <div class="space-y-2">
                                @foreach($addresses as $address)
                                    <label class="flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="saved_address" value="{{ $address->id }}" class="mt-1 mr-3">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $address->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $address->phone }}</div>
                                            <div class="text-sm text-gray-600">{{ $address->full_address }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="text-center mb-4">
                            <span class="text-sm text-gray-500">hoặc</span>
                        </div>
                    @endif

                    <!-- New Address Form -->
                    <div id="new-address-form">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="shipping_address_name" class="block text-sm font-medium text-gray-700">Họ và tên *</label>
                                <input type="text" name="shipping_address[name]" id="shipping_address_name" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="shipping_address_phone" class="block text-sm font-medium text-gray-700">Số điện thoại *</label>
                                <input type="tel" name="shipping_address[phone]" id="shipping_address_phone" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="shipping_address_address_line1" class="block text-sm font-medium text-gray-700">Địa chỉ *</label>
                            <input type="text" name="shipping_address[address_line1]" id="shipping_address_address_line1" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="mt-4">
                            <label for="shipping_address_address_line2" class="block text-sm font-medium text-gray-700">Địa chỉ 2</label>
                            <input type="text" name="shipping_address[address_line2]" id="shipping_address_address_line2"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
                            <div>
                                <label for="shipping_address_city" class="block text-sm font-medium text-gray-700">Tỉnh/Thành phố *</label>
                                <input type="text" name="shipping_address[city]" id="shipping_address_city" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="shipping_address_district" class="block text-sm font-medium text-gray-700">Quận/Huyện *</label>
                                <input type="text" name="shipping_address[district]" id="shipping_address_district" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="shipping_address_ward" class="block text-sm font-medium text-gray-700">Phường/Xã *</label>
                                <input type="text" name="shipping_address[ward]" id="shipping_address_ward" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Phương thức thanh toán</h2>
                    
                    <div class="space-y-3">
                        @guest
                        <!-- Guest checkout - only prepaid methods -->
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="vnpay" checked class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">VNPay</div>
                                <div class="text-sm text-gray-600">Thanh toán qua VNPay (Thẻ ATM, Visa, MasterCard)</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="momo" class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Ví MoMo</div>
                                <div class="text-sm text-gray-600">Thanh toán qua ví MoMo</div>
                            </div>
                        </label>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Đặt hàng không cần đăng ký chỉ hỗ trợ thanh toán trả trước để tránh đơn hàng ảo.
                                    </p>
                                    <div class="mt-2 text-xs text-blue-600">
                                        <strong>Thông tin thẻ test VNPay:</strong><br>
                                        Ngân hàng: NCB | Số thẻ: 9704198526191432198<br>
                                        Tên: NGUYEN VAN A | Ngày: 07/15 | OTP: 123456
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Authenticated users - all methods -->
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Thanh toán khi nhận hàng (COD)</div>
                                <div class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận hàng</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="vnpay" class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">VNPay</div>
                                <div class="text-sm text-gray-600">Thanh toán qua VNPay (Thẻ ATM, Visa, MasterCard)</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Chuyển khoản ngân hàng</div>
                                <div class="text-sm text-gray-600">Chuyển khoản qua ngân hàng</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="momo" class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Ví MoMo</div>
                                <div class="text-sm text-gray-600">Thanh toán qua ví MoMo</div>
                            </div>
                        </label>
                        @endauth
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Ghi chú đơn hàng</h2>
                    <textarea name="notes" rows="3" placeholder="Ghi chú thêm cho đơn hàng..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:col-span-5 lg:mt-0 lg:p-8">
                <h2 class="text-lg font-medium text-gray-900">Tóm tắt đơn hàng</h2>

                <div class="mt-6 space-y-4">
                    @foreach($cart->items as $item)
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
                                <h3 class="text-sm font-medium text-gray-900">{{ $item->variant->product->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    @if($item->variant->size) Size: {{ $item->variant->size }} @endif
                                    @if($item->variant->color) - Màu: {{ $item->variant->color }} @endif
                                </p>
                                <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($item->subtotal, 0, ',', '.') }}đ
                            </div>
                        </div>
                    @endforeach
                </div>

                <dl class="mt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Tạm tính</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($cart->total, 0, ',', '.') }}đ</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Phí vận chuyển</dt>
                        <dd class="text-sm font-medium text-gray-900">Miễn phí</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-base font-medium text-gray-900">Tổng cộng</dt>
                        <dd class="text-base font-medium text-gray-900">{{ number_format($cart->total, 0, ',', '.') }}đ</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <button type="submit" id="place-order-btn"
                            class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Đặt hàng
                    </button>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('cart.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order-btn');
    
    // Handle saved address selection
    const savedAddressRadios = document.querySelectorAll('input[name="saved_address"]');
    const newAddressForm = document.getElementById('new-address-form');
    const newAddressInputs = newAddressForm.querySelectorAll('input[required]');
    
    savedAddressRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                newAddressForm.style.display = 'none';
                // Remove required attribute when form is hidden
                newAddressInputs.forEach(input => {
                    input.removeAttribute('required');
                });
            }
        });
    });
    
    // Show new address form if no saved address is selected
    const checkSavedAddress = () => {
        const selectedSavedAddress = document.querySelector('input[name="saved_address"]:checked');
        if (selectedSavedAddress) {
            newAddressForm.style.display = 'none';
            // Remove required attribute when form is hidden
            newAddressInputs.forEach(input => {
                input.removeAttribute('required');
            });
        } else {
            newAddressForm.style.display = 'block';
            // Add required attribute when form is visible
            newAddressInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
        }
    };
    
    // Initial check
    checkSavedAddress();
    
    // OTP functionality for guest checkout
    let otpVerified = false;
    let otpCode = null;
    const isGuest = document.getElementById('guest_email') !== null;
    
    if (isGuest) {
        // Send OTP
        document.getElementById('send-otp-btn').addEventListener('click', function() {
            const phone = document.getElementById('guest_phone').value;
            const email = document.getElementById('guest_email').value;
            
            if (!phone || !email) {
                showNotification('Vui lòng nhập đầy đủ email và số điện thoại', 'error');
                return;
            }
            
            // Mock OTP generation
            otpCode = Math.floor(100000 + Math.random() * 900000).toString();
            
            // Show OTP section
            document.getElementById('otp-section').classList.remove('hidden');
            
            // Disable send button temporarily
            this.disabled = true;
            this.textContent = 'Đã gửi';
            
            // Show notification
            showNotification(`Mã OTP: ${otpCode} (Mock - trong thực tế sẽ gửi SMS)`, 'success');
            
            // Re-enable after 30 seconds
            setTimeout(() => {
                this.disabled = false;
                this.textContent = 'Gửi lại OTP';
            }, 30000);
        });
        
        // Verify OTP
        document.getElementById('verify-otp-btn').addEventListener('click', function() {
            const inputOtp = document.getElementById('otp_code').value;
            
            if (!inputOtp) {
                showNotification('Vui lòng nhập mã OTP', 'error');
                return;
            }
            
            if (inputOtp === otpCode) {
                otpVerified = true;
                document.getElementById('otp_code').disabled = true;
                this.disabled = true;
                this.textContent = 'Đã xác thực';
                this.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                this.classList.add('bg-green-600');
                
                showNotification('Xác thực OTP thành công!', 'success');
            } else {
                showNotification('Mã OTP không đúng', 'error');
            }
        });
    }
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check OTP verification for guest
        if (isGuest && !otpVerified) {
            showNotification('Vui lòng xác thực OTP trước khi đặt hàng', 'error');
            return;
        }
        
        const originalText = placeOrderBtn.textContent;
        placeOrderBtn.disabled = true;
        placeOrderBtn.textContent = 'Đang xử lý...';
        
        // Prepare form data
        const formData = new FormData();
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add payment method
        formData.append('payment_method', document.querySelector('input[name="payment_method"]:checked').value);
        
        // Add notes
        const notes = document.querySelector('textarea[name="notes"]').value;
        if (notes) {
            formData.append('notes', notes);
        }
        
        // Add guest information
        if (isGuest) {
            formData.append('guest_email', document.getElementById('guest_email').value);
            formData.append('guest_phone', document.getElementById('guest_phone').value);
        }
        
        // Add address data
        const selectedSavedAddress = document.querySelector('input[name="saved_address"]:checked');
        if (selectedSavedAddress) {
            formData.append('saved_address', selectedSavedAddress.value);
        } else {
            // Add new address data
            const addressInputs = document.querySelectorAll('#new-address-form input');
            addressInputs.forEach(input => {
                if (input.name && input.value) {
                    formData.append(input.name, input.value);
                }
            });
        }
        
        fetch('/checkout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        })
        .finally(() => {
            placeOrderBtn.disabled = false;
            placeOrderBtn.textContent = originalText;
        });
    });
    
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
});
</script>
@endsection
