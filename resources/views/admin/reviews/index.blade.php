@extends('admin.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Quản lý đánh giá</h1>
                    <div class="flex space-x-2">
                        <button type="button" id="bulk-approve-btn" 
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 disabled:bg-gray-400"
                                disabled>
                            Duyệt hàng loạt
                        </button>
                        <button type="button" id="bulk-reject-btn" 
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 disabled:bg-gray-400"
                                disabled>
                            Từ chối hàng loạt
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Tên sản phẩm, người dùng..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá</label>
                            <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 sao</option>
                                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 sao</option>
                                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 sao</option>
                                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 sao</option>
                                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 sao</option>
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Lọc
                            </button>
                            <a href="{{ route('admin.reviews.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Reviews Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" style="min-width: 1200px;">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sản phẩm
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Người đánh giá
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Đánh giá
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nội dung
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày tạo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reviews as $review)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="review-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                               value="{{ $review->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                @if($review->product->getFirstMediaUrl('images'))
                                                    <img src="{{ $review->product->getFirstMediaUrl('images', 'thumb') }}" 
                                                         alt="{{ $review->product->name }}"
                                                         class="h-10 w-10 rounded-lg object-cover">
                                                @else
                                                    <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $review->product->name }}</div>
                                                <div class="text-sm text-gray-500">SKU: {{ $review->product->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $review->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            @if($review->title)
                                                <div class="font-medium">{{ $review->title }}</div>
                                            @endif
                                            <div class="text-gray-600">{{ Str::limit($review->content, 100) }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($review->status === 'approved') bg-green-100 text-green-800
                                            @elseif($review->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            @if($review->status === 'approved') Đã duyệt
                                            @elseif($review->status === 'rejected') Đã từ chối
                                            @else Chờ duyệt
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $review->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.reviews.show', $review) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Xem</a>
                                            @if($review->status === 'pending')
                                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Duyệt</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Từ chối</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline" 
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa review này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Không có review nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Forms -->
<form id="bulk-approve-form" method="POST" action="{{ route('admin.reviews.bulk-approve') }}" style="display: none;">
    @csrf
    <input type="hidden" name="review_ids" id="bulk-approve-ids">
</form>

<form id="bulk-reject-form" method="POST" action="{{ route('admin.reviews.bulk-reject') }}" style="display: none;">
    @csrf
    <input type="hidden" name="review_ids" id="bulk-reject-ids">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
    const bulkApproveBtn = document.getElementById('bulk-approve-btn');
    const bulkRejectBtn = document.getElementById('bulk-reject-btn');
    const bulkApproveForm = document.getElementById('bulk-approve-form');
    const bulkRejectForm = document.getElementById('bulk-reject-form');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        reviewCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkButtons();
    });

    // Individual checkbox change
    reviewCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkButtons();
            updateSelectAllState();
        });
    });

    function updateBulkButtons() {
        const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
        const hasChecked = checkedBoxes.length > 0;
        
        bulkApproveBtn.disabled = !hasChecked;
        bulkRejectBtn.disabled = !hasChecked;
    }

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
        const totalBoxes = reviewCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Bulk actions
    bulkApproveBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (ids.length > 0) {
            document.getElementById('bulk-approve-ids').value = JSON.stringify(ids);
            bulkApproveForm.submit();
        }
    });

    bulkRejectBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (ids.length > 0) {
            document.getElementById('bulk-reject-ids').value = JSON.stringify(ids);
            bulkRejectForm.submit();
        }
    });
});
</script>
@endsection
