@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Chi tiết đánh giá</h1>
                    <div class="flex space-x-2">
                        @if($review->status === 'pending')
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Duyệt review
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                    Từ chối review
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.reviews.index') }}" 
                           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Quay lại
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Review Content -->
                    <div class="lg:col-span-2">
                        <!-- Product Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin sản phẩm</h3>
                            <div class="flex items-start space-x-4">
                                <div class="h-20 w-20 flex-shrink-0">
                                    @if($review->product->getFirstMediaUrl('images'))
                                        <img src="{{ $review->product->getFirstMediaUrl('images', 'thumb') }}" 
                                             alt="{{ $review->product->name }}"
                                             class="h-20 w-20 rounded-lg object-cover">
                                    @else
                                        <div class="h-20 w-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $review->product->name }}</h4>
                                    <p class="text-sm text-gray-600">SKU: {{ $review->product->sku }}</p>
                                    <p class="text-sm text-gray-600">Danh mục: {{ $review->product->category->name }}</p>
                                    <p class="text-sm text-gray-600">Thương hiệu: {{ $review->product->brand->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Details -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Nội dung đánh giá</h3>
                            
                            <!-- Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá</label>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-6 w-6 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-lg font-medium text-gray-900">{{ $review->rating }}/5 sao</span>
                                </div>
                            </div>

                            <!-- Title -->
                            @if($review->title)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề</label>
                                    <p class="text-gray-900 font-medium">{{ $review->title }}</p>
                                </div>
                            @endif

                            <!-- Content -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung</label>
                                <div class="bg-white p-4 rounded-lg border">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $review->content }}</p>
                                </div>
                            </div>

                            <!-- Review Images -->
                            @if($review->images && $review->images->count() > 0)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh đính kèm</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($review->images as $image)
                                            <div class="relative">
                                                <img src="{{ $image->image_path }}" 
                                                     alt="Review image" 
                                                     class="w-full h-32 object-cover rounded-lg border">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- User Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin người đánh giá</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Thành viên từ</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Status -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Trạng thái</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái hiện tại</label>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($review->status === 'approved') bg-green-100 text-green-800
                                            @elseif($review->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            @if($review->status === 'approved') Đã duyệt
                                            @elseif($review->status === 'rejected') Đã từ chối
                                            @else Chờ duyệt
                                            @endif
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ngày tạo</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cập nhật lần cuối</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $review->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Xác thực mua hàng</label>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $review->is_verified_purchase ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $review->is_verified_purchase ? 'Đã xác thực' : 'Chưa xác thực' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Thao tác</h3>
                            <div class="space-y-2">
                                @if($review->status === 'pending')
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            Duyệt review
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            Từ chối review
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" 
                                      onsubmit="return confirm('Bạn có chắc muốn xóa review này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                        Xóa review
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
