@extends('layouts.app')

@section('title', 'Chỉnh sửa đánh giá')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa đánh giá</h1>
            <p class="text-sm text-gray-600 mt-1">
                Sản phẩm: <a href="{{ route('products.show', $review->product) }}" class="text-indigo-600 hover:text-indigo-800">
                    {{ $review->product->name }}
                </a>
            </p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('reviews.update', $review) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <!-- Rating -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá của bạn</label>
                    <div class="flex space-x-1" id="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-rating text-2xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400" data-rating="{{ $i }}">
                                ★
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ $review->rating }}" required>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề (tùy chọn)</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $review->title) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                           placeholder="Tóm tắt đánh giá của bạn">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung đánh giá *</label>
                    <textarea id="content" name="content" rows="6" required
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-500 @enderror"
                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này...">{{ old('content', $review->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Images -->
                @if($review->images->count() > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh hiện tại</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($review->images as $image)
                                <div class="relative">
                                    <img src="{{ $image->image_url }}" alt="Review image" 
                                         class="w-full h-24 object-cover rounded-lg">
                                    <button type="button" 
                                            data-image-id="{{ $image->id }}"
                                            class="delete-image-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Images -->
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Thêm ảnh mới (tùy chọn)</label>
                    <input type="file" id="images" name="images[]" multiple accept="image/*"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('images.*') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Có thể tải lên tối đa 5 hình ảnh (JPG, PNG, GIF - tối đa 2MB mỗi ảnh)</p>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('products.show', $review->product) }}" 
                       class="text-gray-600 hover:text-gray-800 text-sm">
                        ← Quay lại sản phẩm
                    </a>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('products.show', $review->product) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Hủy
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Cập nhật đánh giá
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Image Form (Hidden) -->
<form id="delete-image-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="image_id" id="delete-image-id">
</form>

<script>
// Star rating functionality
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating-input');
    
    if (stars.length > 0 && ratingInput) {
        let currentRating = parseInt(ratingInput.value);
        
        // Initialize stars
        updateStars(currentRating);
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                currentRating = index + 1;
                ratingInput.value = currentRating;
                updateStars(currentRating);
            });
            
            star.addEventListener('mouseenter', function() {
                updateStars(index + 1);
            });
        });
        
        document.getElementById('rating-stars').addEventListener('mouseleave', function() {
            updateStars(currentRating);
        });
    }
    
    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
});

// Delete image function
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            if (confirm('Bạn có chắc muốn xóa ảnh này?')) {
                document.getElementById('delete-image-id').value = imageId;
                document.getElementById('delete-image-form').action = '{{ route("reviews.delete-image", $review) }}';
                document.getElementById('delete-image-form').submit();
            }
        });
    });
});
</script>
@endsection
