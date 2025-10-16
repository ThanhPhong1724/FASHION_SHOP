@extends('layouts.app')

@section('title', 'Chỉnh sửa địa chỉ')

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
                    <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa địa chỉ</h1>
                    <p class="mt-2 text-gray-600">Cập nhật thông tin địa chỉ giao hàng</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('addresses.update', $address) }}" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $address->name) }}"
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
                               value="{{ old('phone', $address->phone) }}"
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
                           value="{{ old('address_line1', $address->address_line1) }}"
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
                           value="{{ old('address_line2', $address->address_line2) }}"
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
                            <option value="Hà Nội" {{ old('city', $address->city) == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                            <option value="TP. Hồ Chí Minh" {{ old('city', $address->city) == 'TP. Hồ Chí Minh' ? 'selected' : '' }}>TP. Hồ Chí Minh</option>
                            <option value="Đà Nẵng" {{ old('city', $address->city) == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                            <option value="Hải Phòng" {{ old('city', $address->city) == 'Hải Phòng' ? 'selected' : '' }}>Hải Phòng</option>
                            <option value="Cần Thơ" {{ old('city', $address->city) == 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                            <option value="An Giang" {{ old('city', $address->city) == 'An Giang' ? 'selected' : '' }}>An Giang</option>
                            <option value="Bà Rịa - Vũng Tàu" {{ old('city', $address->city) == 'Bà Rịa - Vũng Tàu' ? 'selected' : '' }}>Bà Rịa - Vũng Tàu</option>
                            <option value="Bắc Giang" {{ old('city', $address->city) == 'Bắc Giang' ? 'selected' : '' }}>Bắc Giang</option>
                            <option value="Bắc Kạn" {{ old('city', $address->city) == 'Bắc Kạn' ? 'selected' : '' }}>Bắc Kạn</option>
                            <option value="Bạc Liêu" {{ old('city', $address->city) == 'Bạc Liêu' ? 'selected' : '' }}>Bạc Liêu</option>
                            <option value="Bắc Ninh" {{ old('city', $address->city) == 'Bắc Ninh' ? 'selected' : '' }}>Bắc Ninh</option>
                            <option value="Bến Tre" {{ old('city', $address->city) == 'Bến Tre' ? 'selected' : '' }}>Bến Tre</option>
                            <option value="Bình Định" {{ old('city', $address->city) == 'Bình Định' ? 'selected' : '' }}>Bình Định</option>
                            <option value="Bình Dương" {{ old('city', $address->city) == 'Bình Dương' ? 'selected' : '' }}>Bình Dương</option>
                            <option value="Bình Phước" {{ old('city', $address->city) == 'Bình Phước' ? 'selected' : '' }}>Bình Phước</option>
                            <option value="Bình Thuận" {{ old('city', $address->city) == 'Bình Thuận' ? 'selected' : '' }}>Bình Thuận</option>
                            <option value="Cà Mau" {{ old('city', $address->city) == 'Cà Mau' ? 'selected' : '' }}>Cà Mau</option>
                            <option value="Cao Bằng" {{ old('city', $address->city) == 'Cao Bằng' ? 'selected' : '' }}>Cao Bằng</option>
                            <option value="Đắk Lắk" {{ old('city', $address->city) == 'Đắk Lắk' ? 'selected' : '' }}>Đắk Lắk</option>
                            <option value="Đắk Nông" {{ old('city', $address->city) == 'Đắk Nông' ? 'selected' : '' }}>Đắk Nông</option>
                            <option value="Điện Biên" {{ old('city', $address->city) == 'Điện Biên' ? 'selected' : '' }}>Điện Biên</option>
                            <option value="Đồng Nai" {{ old('city', $address->city) == 'Đồng Nai' ? 'selected' : '' }}>Đồng Nai</option>
                            <option value="Đồng Tháp" {{ old('city', $address->city) == 'Đồng Tháp' ? 'selected' : '' }}>Đồng Tháp</option>
                            <option value="Gia Lai" {{ old('city', $address->city) == 'Gia Lai' ? 'selected' : '' }}>Gia Lai</option>
                            <option value="Hà Giang" {{ old('city', $address->city) == 'Hà Giang' ? 'selected' : '' }}>Hà Giang</option>
                            <option value="Hà Nam" {{ old('city', $address->city) == 'Hà Nam' ? 'selected' : '' }}>Hà Nam</option>
                            <option value="Hà Tĩnh" {{ old('city', $address->city) == 'Hà Tĩnh' ? 'selected' : '' }}>Hà Tĩnh</option>
                            <option value="Hải Dương" {{ old('city', $address->city) == 'Hải Dương' ? 'selected' : '' }}>Hải Dương</option>
                            <option value="Hậu Giang" {{ old('city', $address->city) == 'Hậu Giang' ? 'selected' : '' }}>Hậu Giang</option>
                            <option value="Hòa Bình" {{ old('city', $address->city) == 'Hòa Bình' ? 'selected' : '' }}>Hòa Bình</option>
                            <option value="Hưng Yên" {{ old('city', $address->city) == 'Hưng Yên' ? 'selected' : '' }}>Hưng Yên</option>
                            <option value="Khánh Hòa" {{ old('city', $address->city) == 'Khánh Hòa' ? 'selected' : '' }}>Khánh Hòa</option>
                            <option value="Kiên Giang" {{ old('city', $address->city) == 'Kiên Giang' ? 'selected' : '' }}>Kiên Giang</option>
                            <option value="Kon Tum" {{ old('city', $address->city) == 'Kon Tum' ? 'selected' : '' }}>Kon Tum</option>
                            <option value="Lai Châu" {{ old('city', $address->city) == 'Lai Châu' ? 'selected' : '' }}>Lai Châu</option>
                            <option value="Lâm Đồng" {{ old('city', $address->city) == 'Lâm Đồng' ? 'selected' : '' }}>Lâm Đồng</option>
                            <option value="Lạng Sơn" {{ old('city', $address->city) == 'Lạng Sơn' ? 'selected' : '' }}>Lạng Sơn</option>
                            <option value="Lào Cai" {{ old('city', $address->city) == 'Lào Cai' ? 'selected' : '' }}>Lào Cai</option>
                            <option value="Long An" {{ old('city', $address->city) == 'Long An' ? 'selected' : '' }}>Long An</option>
                            <option value="Nam Định" {{ old('city', $address->city) == 'Nam Định' ? 'selected' : '' }}>Nam Định</option>
                            <option value="Nghệ An" {{ old('city', $address->city) == 'Nghệ An' ? 'selected' : '' }}>Nghệ An</option>
                            <option value="Ninh Bình" {{ old('city', $address->city) == 'Ninh Bình' ? 'selected' : '' }}>Ninh Bình</option>
                            <option value="Ninh Thuận" {{ old('city', $address->city) == 'Ninh Thuận' ? 'selected' : '' }}>Ninh Thuận</option>
                            <option value="Phú Thọ" {{ old('city', $address->city) == 'Phú Thọ' ? 'selected' : '' }}>Phú Thọ</option>
                            <option value="Phú Yên" {{ old('city', $address->city) == 'Phú Yên' ? 'selected' : '' }}>Phú Yên</option>
                            <option value="Quảng Bình" {{ old('city', $address->city) == 'Quảng Bình' ? 'selected' : '' }}>Quảng Bình</option>
                            <option value="Quảng Nam" {{ old('city', $address->city) == 'Quảng Nam' ? 'selected' : '' }}>Quảng Nam</option>
                            <option value="Quảng Ngãi" {{ old('city', $address->city) == 'Quảng Ngãi' ? 'selected' : '' }}>Quảng Ngãi</option>
                            <option value="Quảng Ninh" {{ old('city', $address->city) == 'Quảng Ninh' ? 'selected' : '' }}>Quảng Ninh</option>
                            <option value="Quảng Trị" {{ old('city', $address->city) == 'Quảng Trị' ? 'selected' : '' }}>Quảng Trị</option>
                            <option value="Sóc Trăng" {{ old('city', $address->city) == 'Sóc Trăng' ? 'selected' : '' }}>Sóc Trăng</option>
                            <option value="Sơn La" {{ old('city', $address->city) == 'Sơn La' ? 'selected' : '' }}>Sơn La</option>
                            <option value="Tây Ninh" {{ old('city', $address->city) == 'Tây Ninh' ? 'selected' : '' }}>Tây Ninh</option>
                            <option value="Thái Bình" {{ old('city', $address->city) == 'Thái Bình' ? 'selected' : '' }}>Thái Bình</option>
                            <option value="Thái Nguyên" {{ old('city', $address->city) == 'Thái Nguyên' ? 'selected' : '' }}>Thái Nguyên</option>
                            <option value="Thanh Hóa" {{ old('city', $address->city) == 'Thanh Hóa' ? 'selected' : '' }}>Thanh Hóa</option>
                            <option value="Thừa Thiên Huế" {{ old('city', $address->city) == 'Thừa Thiên Huế' ? 'selected' : '' }}>Thừa Thiên Huế</option>
                            <option value="Tiền Giang" {{ old('city', $address->city) == 'Tiền Giang' ? 'selected' : '' }}>Tiền Giang</option>
                            <option value="Trà Vinh" {{ old('city', $address->city) == 'Trà Vinh' ? 'selected' : '' }}>Trà Vinh</option>
                            <option value="Tuyên Quang" {{ old('city', $address->city) == 'Tuyên Quang' ? 'selected' : '' }}>Tuyên Quang</option>
                            <option value="Vĩnh Long" {{ old('city', $address->city) == 'Vĩnh Long' ? 'selected' : '' }}>Vĩnh Long</option>
                            <option value="Vĩnh Phúc" {{ old('city', $address->city) == 'Vĩnh Phúc' ? 'selected' : '' }}>Vĩnh Phúc</option>
                            <option value="Yên Bái" {{ old('city', $address->city) == 'Yên Bái' ? 'selected' : '' }}>Yên Bái</option>
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
                               value="{{ old('district', $address->district) }}"
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
                               value="{{ old('ward', $address->ward) }}"
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
                           value="{{ old('postal_code', $address->postal_code) }}"
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
                            <option value="shipping" {{ old('type', $address->type) == 'shipping' ? 'selected' : '' }}>Địa chỉ giao hàng</option>
                            <option value="billing" {{ old('type', $address->type) == 'billing' ? 'selected' : '' }}>Địa chỉ thanh toán</option>
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
                               {{ old('is_default', $address->is_default) ? 'checked' : '' }}
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
                        Cập nhật địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection