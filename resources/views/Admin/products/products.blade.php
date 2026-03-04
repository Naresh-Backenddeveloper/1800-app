@extends('layouts.app')

@section('content')
<div class="space-y-6 p-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Ads Management</h1>
            <p class="text-gray-600 mt-1">Review, approve, reject and manage classified advertisements</p>
        </div>
        <div>
            <!-- <button class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <span>↓</span> Export Ads
            </button> -->
            
            <button class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <span>↓</span> Export Ads
            </button>
        </div>

    </div>

    <!-- Quick Stats (optional - can remove if not needed) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Ads</p>
            <p class="text-3xl font-bold mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-3xl font-bold mt-1 text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-3xl font-bold mt-1 text-emerald-600">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Reported</p>
            <p class="text-3xl font-bold mt-1 text-red-600">{{ $stats['reported'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex flex-wrap gap-6 -mb-px">
            <button type="button" data-tab="all" class="tab-btn py-3 px-1 border-b-2 border-emerald-600 text-emerald-600 font-medium">
                All Ads
            </button>
            <button type="button" data-tab="pending" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                Pending <span class="ml-1.5 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">{{ $stats['pending'] ?? 0 }}</span>
            </button>
            <button type="button" data-tab="active" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                Active
            </button>
            <button type="button" data-tab="sold" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                Sold
            </button>
            <button type="button" data-tab="expired" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                Expired
            </button>
            <button type="button" data-tab="reported" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                Reported <span class="ml-1.5 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">{{ $stats['reported'] ?? 0 }}</span>
            </button>
        </nav>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" id="searchInput" placeholder="Search by title, ID, seller name or phone..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition">
            </div>
            <select id="categoryFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition">
                <option value="all">All Categories</option>
                @foreach ($categories ?? [] as $cat)
                <option value="{{ $cat->id }}">{{ $cat->categorie }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Ads Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800" id="tableTitle">All Advertisements</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ad ID</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Posted</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="adsTable">
                    @forelse ($products as $ad)
                    <tr class="ad-row hover:bg-gray-50 transition-colors"
                        data-status="{{ $ad->status }}"
                        data-category="{{ $ad->category_id ?? $ad->category?->id }}"
                        data-ad='@json($ad)'>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-emerald-600">
                            {{ $ad->ad_id ?? $ad->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $ad->title }}</div>
                            <div class="text-sm text-gray-500 mt-0.5">{{ number_format($ad->views ?? 0) }} views</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $ad->user?->name ?? $ad->user_name ?? '—' }}</div>
                            <div class="text-sm text-gray-500">{{ $ad->user?->mobile ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>{{ $ad->category?->categorie ?? $ad->categorie ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-emerald-600">
                            {{ $ad->formatted_price ?? '₹' . number_format($ad->price ?? 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ match($ad->status) {
                                        'active'   => 'bg-emerald-100 text-emerald-800',
                                        'pending'  => 'bg-yellow-100 text-yellow-800',
                                        'sold'     => 'bg-blue-100 text-blue-800',
                                        'expired'  => 'bg-gray-100 text-gray-800',
                                        'reported' => 'bg-red-100 text-red-800',
                                        default    => 'bg-gray-100 text-gray-800',
                                    } }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $ad->created_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="viewAdDetails(@json($ad))"
                                class="text-emerald-600 hover:text-emerald-800 px-2">View</button>

                            @if ($ad->status === 'PENDING')
                            <button onclick="approveAd(@json($ad))"
                                class="text-green-600 hover:text-green-800 px-2">Approve</button>
                            <button onclick="rejectAd(@json($ad))"
                                class="text-red-600 hover:text-red-800 px-2">Reject</button>
                            @endif

                            <button onclick="deleteAd('{{ $ad->id }}')"
                                class="text-red-600 hover:text-red-800 px-2">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No advertisements found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Store all ads and current ad
    const allAds = <?php echo json_encode($products); ?>;
    let currentAd = null;
    let currentAction = null;

    // Tab switching
    function switchTab(tab) {
        ['all', 'pending', 'active', 'sold', 'expired', 'reported'].forEach(t => {
            const btn = document.getElementById(t + '-tab');
            btn.classList.remove('border-green-500', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        const btn = document.getElementById(tab + '-tab');
        btn.classList.add('border-green-500', 'text-green-600');
        btn.classList.remove('border-transparent', 'text-gray-500');

        const titles = {
            'all': 'All Ads',
            'pending': 'Pending Approval',
            'active': 'Active Ads',
            'sold': 'Sold Ads',
            'expired': 'Expired Ads',
            'reported': 'Reported Ads'
        };
        document.getElementById('tableTitle').textContent = titles[tab];

        filterAds();
    }

    // Filter functionality
    document.getElementById('searchInput').addEventListener('input', filterAds);
    document.getElementById('categoryFilter').addEventListener('change', filterAds);

    function filterAds() {
        const searchQuery = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const currentTab = document.querySelector('.border-green-500').id.replace('-tab', '');

        document.querySelectorAll('.ad-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.dataset.status;
            const category = row.dataset.category;

            const matchesSearch = searchQuery === '' || text.includes(searchQuery);
            const matchesCategory = categoryFilter === 'all' || category === categoryFilter;
            const matchesTab = currentTab === 'all' || status === currentTab.charAt(0).toUpperCase() + currentTab.slice(1);

            row.style.display = matchesSearch && matchesCategory && matchesTab ? '' : 'none';
        });
    }

    function viewAdDetails(ad) {
        currentAd = ad;

        document.getElementById('modalTitle').textContent = `Ad Details - ${ad.id}`;
        document.getElementById('ad-title').textContent = ad.title;
        document.getElementById('ad-price').textContent = ad.price;
        document.getElementById('ad-category').textContent = `${ad.category} / ${ad.subcategory}`;
        document.getElementById('ad-location').textContent = ad.location;
        document.getElementById('ad-status').innerHTML = `<span class="badge ${getStatusClass(ad.status)}">${ad.status}</span>`;
        document.getElementById('ad-date').textContent = ad.postedDate;
        document.getElementById('ad-description').textContent = ad.description;
        document.getElementById('ad-seller').textContent = ad.seller;
        document.getElementById('ad-contact').textContent = ad.sellerContact;

        // Reports section
        if (ad.reports > 0) {
            document.getElementById('ad-reports-section').classList.remove('hidden');
            document.getElementById('ad-reports-count').textContent = ad.reports;
        } else {
            document.getElementById('ad-reports-section').classList.add('hidden');
        }

        // Actions
        const actionsDiv = document.getElementById('modal-actions');
        actionsDiv.innerHTML = '';

        if (ad.status === 'Pending') {
            actionsDiv.innerHTML = `
            <button onclick='approveAd(${JSON.stringify(ad)})' class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                ✅ Approve
            </button>
            <button onclick='rejectAd(${JSON.stringify(ad)})' class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                ❌ Reject
            </button>
        `;
        }

        actionsDiv.innerHTML += `
        <button onclick="deleteAd('${ad.id}')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            🗑️ Delete Ad
        </button>
    `;

        document.getElementById('adModal').classList.remove('hidden');
    }

    function getStatusClass(status) {
        const classes = {
            'Active': 'bg-green-100 text-green-800 border-green-200',
            'Pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'Sold': 'bg-blue-100 text-blue-800 border-blue-200',
            'Expired': 'bg-red-100 text-red-800 border-red-200',
            'Reported': 'bg-red-100 text-red-800 border-red-200',
        };
        return classes[status] || '';
    }

    function closeModal(event) {
        if (!event || event.target.id === 'adModal') {
            document.getElementById('adModal').classList.add('hidden');
        }
    }

    function approveAd(ad) {
        currentAd = ad;
        currentAction = 'approve';
        document.getElementById('approvalTitle').textContent = `Approve Ad - ${ad.id}`;
        document.getElementById('approvalMessage').textContent = `Are you sure you want to approve "${ad.title}"?`;
        document.getElementById('rejectionReasonDiv').classList.add('hidden');
        document.getElementById('confirmBtn').textContent = 'Approve';
        document.getElementById('confirmBtn').className = 'btn-primary flex-1';
        document.getElementById('approvalModal').classList.remove('hidden');
    }

    function rejectAd(ad) {
        currentAd = ad;
        currentAction = 'reject';
        document.getElementById('approvalTitle').textContent = `Reject Ad - ${ad.id}`;
        document.getElementById('approvalMessage').textContent = `Are you sure you want to reject "${ad.title}"?`;
        document.getElementById('rejectionReasonDiv').classList.remove('hidden');
        document.getElementById('confirmBtn').textContent = 'Reject';
        document.getElementById('confirmBtn').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex-1';
        document.getElementById('approvalModal').classList.remove('hidden');
    }

    function confirmApprovalAction() {
        if (currentAction === 'approve') {
            showNotification(`Ad ${currentAd.id} approved successfully`, 'success');
        } else if (currentAction === 'reject') {
            const reason = document.getElementById('rejectionReason').value;
            if (!reason) {
                alert('Please enter a rejection reason');
                return;
            }
            showNotification(`Ad ${currentAd.id} rejected. Reason: ${reason}`, 'success');
        }
        closeApprovalModal();
        closeModal();
        setTimeout(() => location.reload(), 1500);
    }

    function closeApprovalModal(event) {
        if (!event || event.target.id === 'approvalModal') {
            document.getElementById('approvalModal').classList.add('hidden');
            document.getElementById('rejectionReason').value = '';
        }
    }

    function editAd(ad) {
        alert(`Edit functionality for ad: ${ad.title}\n\nThis would open an edit form with all ad details.`);
    }

    function deleteAd(adId) {
        if (confirmAction(`Are you sure you want to delete ad ${adId}? This action cannot be undone.`)) {
            showNotification(`Ad ${adId} deleted successfully`, 'success');
            closeModal();
            setTimeout(() => location.reload(), 1500);
        }
    }

    // Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
            closeApprovalModal();
        }
    });
</script>

@endsection