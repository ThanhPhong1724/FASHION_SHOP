@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Tạo mã giảm giá mới</h1>
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Quay lại
                    </a>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.coupons.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Mã giảm giá *</label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="VD: SALE20, WELCOME10">
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên mã giảm giá *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="VD: Giảm 20% cho đơn hàng đầu tiên">
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea id="description" name="description" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Mô tả chi tiết về mã giảm giá...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Discount Configuration -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Cấu hình giảm giá</h3>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Loại giảm giá *</label>
                            <select id="type" name="type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Chọn loại</option>
                                <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                            </select>
                        </div>

                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700">Giá trị *</label>
                            <input type="number" id="value" name="value" value="{{ old('value') }}" required min="0" step="0.01"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="VD: 20 (cho %) hoặc 50000 (cho ₫)">
                        </div>

                        <div id="max-discount-field" class="hidden">
                            <label for="max_discount" class="block text-sm font-medium text-gray-700">Giảm tối đa (₫)</label>
                            <input type="number" id="max_discount" name="max_discount" value="{{ old('max_discount') }}" min="0" step="0.01"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="VD: 100000">
                        </div>

                        <div>
                            <label for="min_order_amount" class="block text-sm font-medium text-gray-700">Đơn hàng tối thiểu (₫) *</label>
                            <input type="number" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" required min="0" step="0.01"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="VD: 500000">
                        </div>

                        <!-- Usage Limits -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Giới hạn sử dụng</h3>
                        </div>

                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700">Giới hạn lượt sử dụng</label>
                            <input type="number" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Để trống = không giới hạn">
                        </div>

                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700">Bắt đầu từ</label>
                            <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">Hết hạn lúc</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Trạng thái</h3>
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Kích hoạt mã giảm giá
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.coupons.index') }}" 
                           class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                            Hủy
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Tạo mã giảm giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const maxDiscountField = document.getElementById('max-discount-field');
    const valueInput = document.getElementById('value');
    const maxDiscountInput = document.getElementById('max_discount');

    function toggleMaxDiscountField() {
        if (typeSelect.value === 'percentage') {
            maxDiscountField.classList.remove('hidden');
            maxDiscountInput.required = false;
        } else {
            maxDiscountField.classList.add('hidden');
            maxDiscountInput.required = false;
        }
    }

    typeSelect.addEventListener('change', toggleMaxDiscountField);
    toggleMaxDiscountField(); // Initial call
});
</script>
@endsection

