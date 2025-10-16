@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Chỉnh sửa người dùng: {{ $user->name }}</h1>
                    <a href="{{ route('admin.users.show', $user) }}" 
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

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                            <input type="password" id="password" name="password"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Để trống nếu không đổi">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Roles -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Vai trò</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                               {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                            {{ ucfirst($role->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- User Status -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Trạng thái</h3>
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ ($user->is_active ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Tài khoản hoạt động
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.users.show', $user) }}" 
                           class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                            Hủy
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
