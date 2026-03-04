@extends('layouts.app')

@section('content')

<div class="p-6 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{$category->categorie}} Sub-Category Management</h1>
            <p class="text-gray-600 mt-1">Organize and manage marketplace categories and subcategories</p>
        </div>
        <button type="button"
            class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
            <a href="{{url('_admin/secure/categories/sub/add/'.$category->id)}}"><span class="text-xl">➕</span> Add Sub-Category</a>
        </button>
    </div>

    <!-- Stats Cards -->
    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Sub Categories</p>
            <p class="text-3xl font-bold mt-1 text-gray-900">{{ count($data) ?? 0 }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Main Categories</p>
            <p class="text-3xl font-bold mt-1 text-emerald-600">{{ $category->categorie ?? 0 }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Sub Varients</p>
            <p class="text-3xl font-bold mt-1 text-blue-600">{{ $stats['sub'] ?? 0 }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Most Active</p>
            <p class="text-xl font-bold mt-1 text-purple-700">{{ $stats['most_active_name'] ?? '—' }}</p>
            <p class="text-sm text-purple-600 mt-1">{{ number_format($stats['most_active_ads'] ?? 0) }} ads</p>
        </div>
    </div> -->

    <!-- Categories Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">All Sub Categories</h3>
            <div class="flex gap-3">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium flex items-center gap-2">
                    <span>📊</span> Reorder
                </button>
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium flex items-center gap-2">
                    <span>📥</span> Export
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">Icon</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sub-Category Name</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ads</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($data as $category)
                    <tr class="{{ $category->parent_id ? 'bg-gray-50/60' : '' }} hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-2xl text-center">
                            @if(!empty($category->category_icon))
                            <img src="{{ url('cloud/'.$category->category_icon) }}"
                                alt="Category Icon"
                                class="w-10 h-10 mx-auto object-contain">
                            @else
                            📁
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $category->sub_categorie }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ number_format($category->ads_count ?? 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ $category->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->status ?? 'Active' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button"
                                class="text-emerald-600 hover:text-emerald-800 px-2"><a href="{{url('_admin/secure/categories/sub/edit/'.$category->id)}}">Edit</a></button>

                            <button type="button"
                                class="text-blue-600 hover:text-blue-800 px-2"><a href="{{url('_admin/secure/categories/specification/'.$category->id)}}">+ Spec </a></button>

                            <button type="button"
                                class="text-red-600 hover:text-red-800 px-2"><a href="{{url('_admin/secure/categories/sub/delete/'.$category->id)}}">Delete </a></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No categories found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (if using paginate) -->
        @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

<!-- JavaScript placeholders (expand as needed) -->
<script>
    function addCategory() {
        alert('Open modal to create new category');

    }

    function editCategory(id) {
        alert('Editing category ID: ' + id);
    }

    function addSubCategory(parentId) {
        alert('Adding sub-category under parent ID: ' + parentId);
    }

    function deleteCategory(id) {
        if (confirm('Delete this category and all sub-categories?')) {
            alert('Category ' + id + ' would be deleted');
            // Add real delete logic (preferably via fetch/axios + CSRF)
        }
    }
</script>

@endsection