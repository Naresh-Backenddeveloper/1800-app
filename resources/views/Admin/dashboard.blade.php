
@extends('layouts.app')

@section('content')
<?php
// KPI Data
$kpiData = [
    ['title' => 'Total Users', 'value' => number_format($totalUsers), 'change' => '+12.5%', 'trend' => 'up', 'icon' => '👥', 'color' => 'bg-blue-500'],
    ['title' => 'Total Active Ads', 'value' => number_format($totalActiveadds), 'change' => '+8.2%', 'trend' => 'up', 'icon' => '🛍️', 'color' => 'bg-green-500'],
    ['title' => 'Total Sold Ads', 'value' => number_format($totalsoldAdds), 'change' => '+15.3%', 'trend' => 'up', 'icon' => '✅', 'color' => 'bg-purple-500'],
    ['title' => 'Total Revenue', 'value' => number_format($totalRevenue), 'change' => '-3.1%', 'trend' => 'down', 'icon' => '₹', 'color' => 'bg-orange-500'],
];

// Recent Users
$recentUsers = [
    ['id' => 'U12458', 'name' => 'Rajesh Kumar', 'mobile' => '+91 98765 43210', 'role' => 'Seller', 'location' => 'Mumbai, MH', 'joinedDate' => '2026-02-24'],
    ['id' => 'U12457', 'name' => 'Priya Sharma', 'mobile' => '+91 98765 43211', 'role' => 'Buyer', 'location' => 'Delhi, DL', 'joinedDate' => '2026-02-24'],
    ['id' => 'U12456', 'name' => 'Amit Patel', 'mobile' => '+91 98765 43212', 'role' => 'Seller', 'location' => 'Ahmedabad, GJ', 'joinedDate' => '2026-02-23'],
    ['id' => 'U12455', 'name' => 'Sneha Reddy', 'mobile' => '+91 98765 43213', 'role' => 'Buyer', 'location' => 'Hyderabad, TS', 'joinedDate' => '2026-02-23'],
    ['id' => 'U12454', 'name' => 'Vikram Singh', 'mobile' => '+91 98765 43214', 'role' => 'Seller', 'location' => 'Pune, MH', 'joinedDate' => '2026-02-22'],
];

// Recent Ads
$recentAds = [
    ['id' => 'AD3847', 'title' => 'iPhone 15 Pro Max', 'seller' => 'Rajesh Kumar', 'category' => 'Electronics', 'price' => '₹1,25,000', 'status' => 'Active', 'postedDate' => '2026-02-25'],
    ['id' => 'AD3846', 'title' => 'Honda City 2022', 'seller' => 'Amit Patel', 'category' => 'Vehicles', 'price' => '₹12,50,000', 'status' => 'Active', 'postedDate' => '2026-02-25'],
    ['id' => 'AD3845', 'title' => '2BHK Apartment', 'seller' => 'Vikram Singh', 'category' => 'Real Estate', 'price' => '₹65,00,000', 'status' => 'Pending', 'postedDate' => '2026-02-24'],
    ['id' => 'AD3844', 'title' => 'Sofa Set 5 Seater', 'seller' => 'Rajesh Kumar', 'category' => 'Furniture', 'price' => '₹28,000', 'status' => 'Active', 'postedDate' => '2026-02-24'],
    ['id' => 'AD3843', 'title' => 'MacBook Pro M3', 'seller' => 'Amit Patel', 'category' => 'Electronics', 'price' => '₹1,85,000', 'status' => 'Sold', 'postedDate' => '2026-02-23'],
];

// Pending Approvals
$pendingApprovals = [
    ['id' => 'AD3850', 'title' => 'Samsung Galaxy S24', 'seller' => 'Neha Gupta', 'category' => 'Electronics', 'submittedDate' => '2026-02-25', 'priority' => 'High'],
    ['id' => 'AD3849', 'title' => 'Royal Enfield 350', 'seller' => 'Karan Mehta', 'category' => 'Vehicles', 'submittedDate' => '2026-02-25', 'priority' => 'Medium'],
    ['id' => 'AD3848', 'title' => 'Gaming PC Setup', 'seller' => 'Rohan Das', 'category' => 'Electronics', 'submittedDate' => '2026-02-24', 'priority' => 'Low'],
];
?>

<!-- Dashboard Content -->
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 mt-1">Welcome back! Here's what's happening today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($kpiData as $kpi): ?>
            <div class="admin-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500"><?php echo $kpi['title']; ?></p>
                        <p class="text-2xl font-bold mt-1"><?php echo $kpi['value']; ?></p>
                        <div class="flex items-center gap-1 mt-2 text-sm <?php echo $kpi['trend'] === 'up' ? 'text-green-600' : 'text-red-600'; ?>">
                            <span><?php echo $kpi['trend'] === 'up' ? '↗' : '↘'; ?></span>
                            <span><?php echo $kpi['change']; ?></span>
                        </div>
                    </div>
                    <div class="<?php echo $kpi['color']; ?> w-12 h-12 rounded-lg flex items-center justify-center">
                        <span class="text-white text-2xl"><?php echo $kpi['icon']; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ads by Category -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="text-lg font-semibold">Ads by Category</h3>
            </div>
            <div class="admin-card-content">
                <canvas id="categoryChart" style="height: 250px;"></canvas>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="admin-card lg:col-span-2">
            <div class="admin-card-header">
                <h3 class="text-lg font-semibold">Monthly Revenue</h3>
            </div>
            <div class="admin-card-content">
                <canvas id="revenueChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="text-lg font-semibold">User Growth</h3>
        </div>
        <div class="admin-card-content">
            <canvas id="userGrowthChart" style="height: 300px;"></canvas>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="admin-card">
            <div class="admin-card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold">Recent Users</h3>
                <a href="users.php" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>
            <div class="admin-card-content">
                <div class="space-y-4">
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <p class="font-medium"><?php echo $user['name']; ?></p>
                                <p class="text-sm text-gray-500"><?php echo $user['mobile']; ?></p>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-active"><?php echo $user['role']; ?></span>
                                <p class="text-xs text-gray-500 mt-1"><?php echo $user['joinedDate']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recently Posted Ads -->
        <div class="admin-card">
            <div class="admin-card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold">Recently Posted Ads</h3>
                <a href="ads.php" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>
            <div class="admin-card-content">
                <div class="space-y-4">
                    <?php foreach ($recentAds as $ad): ?>
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <p class="font-medium"><?php echo $ad['title']; ?></p>
                                <p class="text-sm text-gray-500"><?php echo $ad['seller']; ?> • <?php echo $ad['category']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-blue-600"><?php echo $ad['price']; ?></p>
                                <span class="badge badge-<?php echo strtolower($ad['status']); ?> mt-1">
                                    <?php echo $ad['status']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="admin-card">
        <div class="admin-card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Pending Approvals</h3>
            <a href="ads.php?status=pending" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="admin-card-content">
            <div class="space-y-3">
                <?php foreach ($pendingApprovals as $item): ?>
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <p class="font-medium"><?php echo $item['title']; ?></p>
                            <p class="text-sm text-gray-500"><?php echo $item['seller']; ?> • <?php echo $item['category']; ?> • <?php echo $item['submittedDate']; ?></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-<?php echo strtolower($item['priority']); ?>">
                                <?php echo $item['priority']; ?>
                            </span>
                            <div class="flex gap-2">
                                <button class="btn-primary text-sm px-3 py-1">Approve</button>
                                <button class="btn-secondary text-sm px-3 py-1">Reject</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    let categoryPie = null;

    async function loadCategoryPieChart() {
        try {
           
            const url = '{{ url("/_admin/pie-stats") }}'; 

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    // If using api.php + sanctum → might need Authorization header or csrf
                }
            });

            if (!response.ok) throw new Error(`Status ${response.status}`);

            const json = await response.json();

            if (!json.success || !json.labels?.length) {
                showNoData();
                return;
            }

            const ctx = document.getElementById('categoryChart').getContext('2d');

            if (categoryPie) categoryPie.destroy();

            categoryPie = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: json.labels,
                    datasets: [{
                        data: json.data,
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899',
                            '#f97316', '#06b6d4', '#84cc16', '#c084fc', '#f43f5e',
                            '#fbbf24', '#22d3ee'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                font: {
                                    size: 13
                                },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    let label = ctx.label || '';
                                    let value = ctx.raw;
                                    let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    let pct = total ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${pct}%)`;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: `Total: ${json.total.toLocaleString()}`,
                            font: {
                                size: 15
                            },
                            padding: {
                                bottom: 12
                            }
                        }
                    }
                }
            });
        } catch (err) {
            console.error(err);
            showNoData('Could not load chart data');
        }
    }

    function showNoData(message = 'No data available') {
        const canvas = document.getElementById('categoryChart');
        canvas.insertAdjacentHTML('afterend', `<p class="text-center text-muted mt-3">${message}</p>`);
    }

    document.addEventListener('DOMContentLoaded', loadCategoryPieChart);

    // Revenue Chart (Bar)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [35000, 42000, 38000, 51000, 49000, 62000],
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // User Growth Chart (Line)
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Users',
                data: [8500, 9200, 9800, 10500, 11200, 12458],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection

