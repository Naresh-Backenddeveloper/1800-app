<?php


define('APP_NAME', 'Pengwin Admin');
define('APP_URL', 'http://localhost/php-admin');
$currentPage = getCurrentPage();
function getCurrentPage()
{
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page === 'index' ? 'dashboard' : $page;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo ucfirst($currentPage); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/custom.css">

    <style>
        /* Custom Green Theme */
        :root {
            --primary-green: #6BA145;
            --primary-green-dark: #5a8f38;
        }

        .bg-primary-green {
            background: linear-gradient(135deg, #6BA145 0%, #5a8f38 100%);
        }

        .text-primary-green {
            color: #6BA145;
        }

        .border-primary-green {
            border-color: #6BA145;
        }

        /* Sidebar Active State */
        .sidebar-active {
            background-color: #6BA145 !important;
            color: white !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #6BA145 0%, #5a8f38 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen overflow-hidden">

        <?php
        $currentPage = getCurrentPage();
        ?>

        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col shadow-2xl h-screen sticky top-0">

            <!-- Logo / Brand -->
            <div class="p-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-emerald-600 text-white font-bold text-lg">
                        PW
                    </div>
                    <div>
                        <h2 class="font-semibold text-lg tracking-tight">Pengwin Admin</h2>
                        <p class="text-xs text-slate-400">Control Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-6 px-3">
                @php
                // Define menu items with route names (recommended way)
                $menuItems = [
                ['icon' => '📊', 'label' => 'Dashboard', 'route' => '_admin/secure'],
                ['icon' => '👥', 'label' => 'Users', 'route' => '_admin/secure/users'],
                ['icon' => '🛍️', 'label' => 'Ads Management','route' => '_admin/secure/adds'],
                ['icon' => '📁', 'label' => 'Categories', 'route' => '_admin/secure/categories'],
                ['icon' => '💬', 'label' => 'Chat & Reports','route' => '_admin/secure/chats'],
                ['icon' => '₹', 'label' => 'Monetization', 'route' => '_admin/secure/monitazation'],
                ['icon' => '💳', 'label' => 'Transactions', 'route' => 'admin.transactions.index'],
                ['icon' => '📈', 'label' => 'Analytics', 'route' => 'admin.analytics.index'],
                ['icon' => '📄', 'label' => 'Content', 'route' => 'admin.content.index'],
                ['icon' => '⚙️', 'label' => 'Settings', 'route' => 'admin.settings.index'],
                ];
                @endphp

                @foreach ($menuItems as $item)
                <a href="{{ url($item['route']) }}"
                    class="flex items-center gap-3 px-4 py-3.5 mx-2 rounded-lg transition-all duration-200 group
                      {{ Route::currentRouteName() === $item['route'] ? 'bg-emerald-600/20 text-emerald-400 font-medium' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <span class="text-2xl opacity-90 group-hover:scale-110 transition-transform">{{ $item['icon'] }}</span>
                    <span class="text-sm">{{ $item['label'] }}</span>
                </a>
                @endforeach
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-700/60 mt-auto">
                <a href="{{ url('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-red-900/30 hover:text-red-400 transition-all duration-200">
                    <span class="text-xl">🚪</span>
                    <span class="text-sm font-medium">Logout</span>
                </a>

                <form id="logout-form" action="{{ url('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
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


                @yield('content')

            </main>
        </div>
    </div>

    <!-- JavaScript for interactivity -->
    <script>
        // Simple notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Confirm dialogs
        function confirmAction(message) {
            return confirm(message);
        }

        // Export to CSV function
        function exportToCSV(filename, data) {
            const csv = convertToCSV(data);
            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
        }

        function convertToCSV(data) {
            const headers = Object.keys(data[0]).join(',');
            const rows = data.map(row => Object.values(row).join(','));
            return headers + '\n' + rows.join('\n');
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('Copied to clipboard!');
            });
        }
    </script>

</body>

</html>