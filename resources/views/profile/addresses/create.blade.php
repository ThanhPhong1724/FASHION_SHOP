@extends('layouts.app')

@section('title', 'Thêm địa chỉ mới')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('addresses.index') }}" 
                   class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Thêm địa chỉ mới</h1>
                    <p class="mt-2 text-gray-600">Thêm địa chỉ giao hàng mới cho tài khoản của bạn</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('addresses.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Name & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Số điện thoại <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-300 @enderror"
                               required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="address_line1" class="block text-sm font-medium text-gray-700">
                        Địa chỉ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="address_line1" 
                           id="address_line1" 
                           value="{{ old('address_line1') }}"
                           placeholder="Số nhà, tên đường"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address_line1') border-red-300 @enderror"
                           required>
                    @error('address_line1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="address_line2" class="block text-sm font-medium text-gray-700">
                        Địa chỉ bổ sung
                    </label>
                    <input type="text" 
                           name="address_line2" 
                           id="address_line2" 
                           value="{{ old('address_line2') }}"
                           placeholder="Tầng, căn hộ, tòa nhà (tùy chọn)"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address_line2') border-red-300 @enderror">
                    @error('address_line2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City, District, Ward -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">
                            Tỉnh/Thành phố <span class="text-red-500">*</span>
                        </label>
                        <select name="city" 
                                id="city" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('city') border-red-300 @enderror"
                                required>
                            <option value="">Chọn tỉnh/thành phố</option>
                            <option value="Hà Nội" {{ old('city') == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                            <option value="TP. Hồ Chí Minh" {{ old('city') == 'TP. Hồ Chí Minh' ? 'selected' : '' }}>TP. Hồ Chí Minh</option>
                            <option value="Đà Nẵng" {{ old('city') == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                            <option value="Hải Phòng" {{ old('city') == 'Hải Phòng' ? 'selected' : '' }}>Hải Phòng</option>
                            <option value="Cần Thơ" {{ old('city') == 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                            <option value="An Giang" {{ old('city') == 'An Giang' ? 'selected' : '' }}>An Giang</option>
                            <option value="Bà Rịa - Vũng Tàu" {{ old('city') == 'Bà Rịa - Vũng Tàu' ? 'selected' : '' }}>Bà Rịa - Vũng Tàu</option>
                            <option value="Bắc Giang" {{ old('city') == 'Bắc Giang' ? 'selected' : '' }}>Bắc Giang</option>
                            <option value="Bắc Kạn" {{ old('city') == 'Bắc Kạn' ? 'selected' : '' }}>Bắc Kạn</option>
                            <option value="Bạc Liêu" {{ old('city') == 'Bạc Liêu' ? 'selected' : '' }}>Bạc Liêu</option>
                            <option value="Bắc Ninh" {{ old('city') == 'Bắc Ninh' ? 'selected' : '' }}>Bắc Ninh</option>
                            <option value="Bến Tre" {{ old('city') == 'Bến Tre' ? 'selected' : '' }}>Bến Tre</option>
                            <option value="Bình Định" {{ old('city') == 'Bình Định' ? 'selected' : '' }}>Bình Định</option>
                            <option value="Bình Dương" {{ old('city') == 'Bình Dương' ? 'selected' : '' }}>Bình Dương</option>
                            <option value="Bình Phước" {{ old('city') == 'Bình Phước' ? 'selected' : '' }}>Bình Phước</option>
                            <option value="Bình Thuận" {{ old('city') == 'Bình Thuận' ? 'selected' : '' }}>Bình Thuận</option>
                            <option value="Cà Mau" {{ old('city') == 'Cà Mau' ? 'selected' : '' }}>Cà Mau</option>
                            <option value="Cao Bằng" {{ old('city') == 'Cao Bằng' ? 'selected' : '' }}>Cao Bằng</option>
                            <option value="Đắk Lắk" {{ old('city') == 'Đắk Lắk' ? 'selected' : '' }}>Đắk Lắk</option>
                            <option value="Đắk Nông" {{ old('city') == 'Đắk Nông' ? 'selected' : '' }}>Đắk Nông</option>
                            <option value="Điện Biên" {{ old('city') == 'Điện Biên' ? 'selected' : '' }}>Điện Biên</option>
                            <option value="Đồng Nai" {{ old('city') == 'Đồng Nai' ? 'selected' : '' }}>Đồng Nai</option>
                            <option value="Đồng Tháp" {{ old('city') == 'Đồng Tháp' ? 'selected' : '' }}>Đồng Tháp</option>
                            <option value="Gia Lai" {{ old('city') == 'Gia Lai' ? 'selected' : '' }}>Gia Lai</option>
                            <option value="Hà Giang" {{ old('city') == 'Hà Giang' ? 'selected' : '' }}>Hà Giang</option>
                            <option value="Hà Nam" {{ old('city') == 'Hà Nam' ? 'selected' : '' }}>Hà Nam</option>
                            <option value="Hà Tĩnh" {{ old('city') == 'Hà Tĩnh' ? 'selected' : '' }}>Hà Tĩnh</option>
                            <option value="Hải Dương" {{ old('city') == 'Hải Dương' ? 'selected' : '' }}>Hải Dương</option>
                            <option value="Hậu Giang" {{ old('city') == 'Hậu Giang' ? 'selected' : '' }}>Hậu Giang</option>
                            <option value="Hòa Bình" {{ old('city') == 'Hòa Bình' ? 'selected' : '' }}>Hòa Bình</option>
                            <option value="Hưng Yên" {{ old('city') == 'Hưng Yên' ? 'selected' : '' }}>Hưng Yên</option>
                            <option value="Khánh Hòa" {{ old('city') == 'Khánh Hòa' ? 'selected' : '' }}>Khánh Hòa</option>
                            <option value="Kiên Giang" {{ old('city') == 'Kiên Giang' ? 'selected' : '' }}>Kiên Giang</option>
                            <option value="Kon Tum" {{ old('city') == 'Kon Tum' ? 'selected' : '' }}>Kon Tum</option>
                            <option value="Lai Châu" {{ old('city') == 'Lai Châu' ? 'selected' : '' }}>Lai Châu</option>
                            <option value="Lâm Đồng" {{ old('city') == 'Lâm Đồng' ? 'selected' : '' }}>Lâm Đồng</option>
                            <option value="Lạng Sơn" {{ old('city') == 'Lạng Sơn' ? 'selected' : '' }}>Lạng Sơn</option>
                            <option value="Lào Cai" {{ old('city') == 'Lào Cai' ? 'selected' : '' }}>Lào Cai</option>
                            <option value="Long An" {{ old('city') == 'Long An' ? 'selected' : '' }}>Long An</option>
                            <option value="Nam Định" {{ old('city') == 'Nam Định' ? 'selected' : '' }}>Nam Định</option>
                            <option value="Nghệ An" {{ old('city') == 'Nghệ An' ? 'selected' : '' }}>Nghệ An</option>
                            <option value="Ninh Bình" {{ old('city') == 'Ninh Bình' ? 'selected' : '' }}>Ninh Bình</option>
                            <option value="Ninh Thuận" {{ old('city') == 'Ninh Thuận' ? 'selected' : '' }}>Ninh Thuận</option>
                            <option value="Phú Thọ" {{ old('city') == 'Phú Thọ' ? 'selected' : '' }}>Phú Thọ</option>
                            <option value="Phú Yên" {{ old('city') == 'Phú Yên' ? 'selected' : '' }}>Phú Yên</option>
                            <option value="Quảng Bình" {{ old('city') == 'Quảng Bình' ? 'selected' : '' }}>Quảng Bình</option>
                            <option value="Quảng Nam" {{ old('city') == 'Quảng Nam' ? 'selected' : '' }}>Quảng Nam</option>
                            <option value="Quảng Ngãi" {{ old('city') == 'Quảng Ngãi' ? 'selected' : '' }}>Quảng Ngãi</option>
                            <option value="Quảng Ninh" {{ old('city') == 'Quảng Ninh' ? 'selected' : '' }}>Quảng Ninh</option>
                            <option value="Quảng Trị" {{ old('city') == 'Quảng Trị' ? 'selected' : '' }}>Quảng Trị</option>
                            <option value="Sóc Trăng" {{ old('city') == 'Sóc Trăng' ? 'selected' : '' }}>Sóc Trăng</option>
                            <option value="Sơn La" {{ old('city') == 'Sơn La' ? 'selected' : '' }}>Sơn La</option>
                            <option value="Tây Ninh" {{ old('city') == 'Tây Ninh' ? 'selected' : '' }}>Tây Ninh</option>
                            <option value="Thái Bình" {{ old('city') == 'Thái Bình' ? 'selected' : '' }}>Thái Bình</option>
                            <option value="Thái Nguyên" {{ old('city') == 'Thái Nguyên' ? 'selected' : '' }}>Thái Nguyên</option>
                            <option value="Thanh Hóa" {{ old('city') == 'Thanh Hóa' ? 'selected' : '' }}>Thanh Hóa</option>
                            <option value="Thừa Thiên Huế" {{ old('city') == 'Thừa Thiên Huế' ? 'selected' : '' }}>Thừa Thiên Huế</option>
                            <option value="Tiền Giang" {{ old('city') == 'Tiền Giang' ? 'selected' : '' }}>Tiền Giang</option>
                            <option value="Trà Vinh" {{ old('city') == 'Trà Vinh' ? 'selected' : '' }}>Trà Vinh</option>
                            <option value="Tuyên Quang" {{ old('city') == 'Tuyên Quang' ? 'selected' : '' }}>Tuyên Quang</option>
                            <option value="Vĩnh Long" {{ old('city') == 'Vĩnh Long' ? 'selected' : '' }}>Vĩnh Long</option>
                            <option value="Vĩnh Phúc" {{ old('city') == 'Vĩnh Phúc' ? 'selected' : '' }}>Vĩnh Phúc</option>
                            <option value="Yên Bái" {{ old('city') == 'Yên Bái' ? 'selected' : '' }}>Yên Bái</option>
                        </select>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700">
                            Quận/Huyện <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="district" 
                               id="district" 
                               value="{{ old('district') }}"
                               placeholder="Quận/Huyện"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('district') border-red-300 @enderror"
                               required>
                        @error('district')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ward" class="block text-sm font-medium text-gray-700">
                            Phường/Xã <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="ward" 
                               id="ward" 
                               value="{{ old('ward') }}"
                               placeholder="Phường/Xã"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('ward') border-red-300 @enderror"
                               required>
                        @error('ward')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">
                        Mã bưu điện
                    </label>
                    <input type="text" 
                           name="postal_code" 
                           id="postal_code" 
                           value="{{ old('postal_code') }}"
                           placeholder="Mã bưu điện (tùy chọn)"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('postal_code') border-red-300 @enderror">
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type & Default -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">
                            Loại địa chỉ <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                id="type" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('type') border-red-300 @enderror"
                                required>
                            <option value="">Chọn loại địa chỉ</option>
                            <option value="shipping" {{ old('type') == 'shipping' ? 'selected' : '' }}>Địa chỉ giao hàng</option>
                            <option value="billing" {{ old('type') == 'billing' ? 'selected' : '' }}>Địa chỉ thanh toán</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_default" 
                               id="is_default" 
                               value="1"
                               {{ old('is_default') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_default" class="ml-2 block text-sm text-gray-900">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('addresses.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Hủy
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Thêm địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection