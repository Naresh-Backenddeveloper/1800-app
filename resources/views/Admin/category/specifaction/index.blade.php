@extends('layouts.app')

@section('content')

<div class="p-6 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{$category->categorie}} specification Management</h1>
            <p class="text-gray-600 mt-1">Organize and manage marketplace {{$category->categorie}} specification </p>
        </div>
        <button type="button"
            class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
            <a href="{{url('_admin/secure/categories/specification/add/'.$category->id)}}"><span class="text-xl">➕</span> Add specification</a>
        </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">All {{$category->categorie}} Specification</h3>
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
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">Id</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Specification Name</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($data as $row)
                    <tr class="bg-gray-50/60 hover:bg-gray-50 transition-colors">

                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $row->id }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $row->specification }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button"
                                class="text-emerald-600 hover:text-emerald-800 px-2"><a href="{{url('_admin/secure/categories/specification/edit/'. $row->id )}}">Edit</a></button>

                            <button type="button"
                                class="text-red-600 hover:text-red-800 px-2"><a href="{{url('_admin/secure/categories/specification/delete/'. $row->id )}}">Delete </a></button>
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