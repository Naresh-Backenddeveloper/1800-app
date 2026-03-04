<?php
if (!defined('APP_NAME')) {
    require_once 'config.php';
}

$currentPage = getCurrentPage();
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
