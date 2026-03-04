<?php
$currentPage = getCurrentPage();

$menuItems = [
    ['icon' => '📊', 'label' => 'Dashboard', 'page' => 'dashboard', 'file' => 'index.php'],
    ['icon' => '👥', 'label' => 'Users', 'page' => 'users', 'file' => 'users.php'],
    ['icon' => '🛍️', 'label' => 'Ads Management', 'page' => 'ads', 'file' => 'ads.php'],
    ['icon' => '📁', 'label' => 'Categories', 'page' => 'categories', 'file' => 'categories.php'],
    ['icon' => '💬', 'label' => 'Chat & Reports', 'page' => 'chat', 'file' => 'chat.php'],
    ['icon' => '₹', 'label' => 'Monetization', 'page' => 'monetization', 'file' => 'monetization.php'],
    ['icon' => '💳', 'label' => 'Transactions', 'page' => 'transactions', 'file' => 'transactions.php'],
    ['icon' => '📈', 'label' => 'Analytics', 'page' => 'analytics', 'file' => 'analytics.php'],
    ['icon' => '📄', 'label' => 'Content', 'page' => 'content', 'file' => 'content.php'],
    ['icon' => '⚙️', 'label' => 'Settings', 'page' => 'settings', 'file' => 'settings.php'],
];
?>

<!-- Sidebar -->
<aside class="w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col shadow-2xl">
    <!-- Logo -->
    <div class="p-4 border-b border-slate-700/50">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-lg bg-primary-green">
                <span class="font-bold text-sm">PW</span>
            </div>
            <span class="font-semibold">Pengwin Admin</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?php echo $item['file']; ?>" 
               class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition-colors <?php echo $currentPage === $item['page'] ? 'sidebar-active' : 'text-gray-300 hover:bg-slate-800'; ?>">
                <span class="text-xl"><?php echo $item['icon']; ?></span>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-slate-700">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white transition-colors">
            <span class="text-xl">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Search Bar -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
                    <input type="text" 
                           placeholder="Search..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none">
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="text-xl">🔔</span>
                    <span class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center text-xs text-white rounded-full bg-primary-green">
                        3
                    </span>
                </button>

                <!-- User Menu -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white bg-primary-green">
                        AD
                    </div>
                    <div class="text-left">
                        <div class="text-sm font-medium">Admin User</div>
                        <div class="text-xs text-gray-500">Super Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 overflow-auto p-6">
