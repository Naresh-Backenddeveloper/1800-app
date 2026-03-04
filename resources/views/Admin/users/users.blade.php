@extends('layouts.app')

@section('content')

<div class="space-y-6 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">Manage all buyers and sellers on the platform</p>
        </div>
        <button class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center gap-2 shadow-sm">
            <span>↓</span> Export Users
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Users</p>
                    <p class="text-2xl font-bold mt-1 text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex items-center justify-center text-2xl">👥</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Users</p>
                    <p class="text-2xl font-bold mt-1 text-green-600">{{ $stats['active_users'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 text-green-600 w-12 h-12 rounded-lg flex items-center justify-center text-2xl">✅</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Blocked Users</p>
                    <p class="text-2xl font-bold mt-1 text-red-600">{{ $stats['blocked_users'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 text-red-600 w-12 h-12 rounded-lg flex items-center justify-center text-2xl">🚫</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Ads</p>
                    <p class="text-2xl font-bold mt-1 text-purple-600">{{ $stats['total_ads'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 text-purple-600 w-12 h-12 rounded-lg flex items-center justify-center text-2xl">📢</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" id="searchInput" placeholder="Search by name, mobile, email or ID..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition">
            </div>
            <select id="statusFilter"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition">
                <option value="all">All Status</option>
                <option value="Active">Active</option>
                <option value="Blocked">Blocked</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">All Users ({{ $users->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User ID</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name & Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mobile</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="usersTable">
                    @forelse ($users as $user)
                    <tr class="user-row hover:bg-gray-50 transition-colors duration-150"
                        data-status="{{ $user->status }}"
                        data-user='@json($user)'>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600">
                            {{ $user->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->mobile ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->location ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full 
                                    {{ $user->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $user->joined_date ? \Carbon\Carbon::parse($user->joined_date)->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button onclick="viewUserDetails(@json($user))"
                                class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" onclick="closeModal(event)">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">User Details</h2>
                    <p class="text-sm text-gray-500" id="modalSubtitle">Loading...</p>
                </div>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b px-6">
            <nav class="flex gap-8 -mb-px">
                <button onclick="switchTab('overview')" class="tab-btn border-b-2 border-green-500 text-green-600 py-4 px-1 font-medium" data-tab="overview">Overview</button>
                <button onclick="switchTab('history')" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium" data-tab="history">History</button>
                <button onclick="switchTab('transactions')" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium" data-tab="transactions">Transactions</button>
            </nav>
        </div>

        <!-- Overview Tab -->
        <div id="overview-content" class="p-6">
            <div class="space-y-6">
                <div>
                    <h3 class="font-semibold mb-3">Profile</h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                        <div class="flex items-center gap-3"><span class="text-gray-500">✉️</span><span id="user-email"></span></div>
                        <div class="flex items-center gap-3"><span class="text-gray-500">📞</span><span id="user-mobile"></span></div>
                        <div class="flex items-center gap-3"><span class="text-gray-500">📍</span><span id="user-location"></span></div>
                        <div class="flex items-center gap-3"><span class="text-gray-500">📅</span><span id="user-joined"></span></div>
                    </div>
                </div>

                <!-- Add more sections as needed -->

                <div class="flex gap-3">
                    <button id="block-btn" onclick="toggleBlockUser()" class="flex-1 bg-gray-100 hover:bg-gray-200 py-2.5 rounded-lg">Block User</button>
                    <button onclick="deleteUser()" class="flex-1 bg-red-600 text-white py-2.5 rounded-lg hover:bg-red-700">Delete User</button>
                </div>
            </div>
        </div>

        <!-- Other tabs (placeholder) -->
        <div id="history-content" class="p-6 hidden">History content goes here...</div>
        <div id="transactions-content" class="p-6 hidden">Transactions content goes here...</div>
    </div>
</div>

<script>
    // ================================================
    // JavaScript - Keep your original logic + fixes
    // ================================================

    let currentUser = null;

    document.getElementById('searchInput')?.addEventListener('input', applyFilters);
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);

    function applyFilters() {
        const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
        const status = document.getElementById('statusFilter')?.value || 'all';

        document.querySelectorAll('.user-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowStatus = row.dataset.status;

            const matchSearch = !search || text.includes(search);
            const matchStatus = status === 'all' || rowStatus === status;

            row.style.display = matchSearch && matchStatus ? '' : 'none';
        });
    }

    function viewUserDetails(user) {
        currentUser = user;

        document.getElementById('modalSubtitle').textContent = `Complete information about ${user.name || 'User'}`;
        document.getElementById('user-email').textContent = user.email || '—';
        document.getElementById('user-mobile').textContent = user.mobile || '—';
        document.getElementById('user-location').textContent = user.location || '—';
        document.getElementById('user-joined').textContent = user.joined_date ?
            new Date(user.joined_date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) :
            '—';

        // Update block button text
        const blockBtn = document.getElementById('block-btn');
        if (blockBtn) {
            blockBtn.textContent = user.status === 'Active' ? '🚫 Block User' : '✅ Unblock User';
        }

        document.getElementById('userModal').classList.remove('hidden');
        switchTab('overview');
    }

    function closeModal(e) {
        if (!e || e.target.id === 'userModal' || e.target.closest('#userModal') === null) {
            document.getElementById('userModal').classList.add('hidden');
        }
    }

    function switchTab(tab) {
        document.querySelectorAll('[id$="-content"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-green-500', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        const content = document.getElementById(tab + '-content');
        const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);

        if (content) content.classList.remove('hidden');
        if (btn) {
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-green-500', 'text-green-600');
        }
    }

    // Placeholder functions (replace with real implementation later)
    function toggleBlockUser() {
        if (!currentUser) return;
        alert(`Would ${currentUser.status === 'Active' ? 'block' : 'unblock'} user: ${currentUser.name}`);
        // Add real AJAX call here
    }

    function deleteUser() {
        if (!currentUser) return;
        if (confirm('Delete this user permanently?')) {
            alert(`User ${currentUser.name} would be deleted`);
          
        }
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
</script>

@endsection