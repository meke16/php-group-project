<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if (!AuthController::checkAuth()) {
    // NOTE: Changed to relative path for better routing practice in PHP front controllers
    header("Location: /login");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$user_initial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PMS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Using Indigo-600 and Indigo-500 for consistency
                        primary: '#4f46e5', 
                        'primary-light': '#6366f1',
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">

<div id="overlay"
     class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
     onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">

<?= include 'sidebar.php' ?>

    <div class="flex-1 flex flex-col overflow-y-auto">

        <header class="sticky top-0 z-20 bg-white dark:bg-gray-800 shadow px-4 py-3 flex justify-between items-center transition-colors duration-300">

            <button onclick="toggleSidebar()" class="lg:hidden text-gray-700 dark:text-gray-200 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>

            <h1 class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">Dashboard</h1>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:hidden">PMS</h1>

            <div class="flex items-center gap-4">
                
                <button id="theme-toggle" type="button" 
                    class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg id="theme-toggle-light-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($username) ?></p>
                        <p class="text-xs text-primary-light dark:text-primary-light font-medium"><?= ucfirst($role) ?></p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-base shadow-lg">
                        <?= $user_initial ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 flex-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Hello, <span class="text-primary-light dark:text-primary-light"><?= htmlspecialchars($username) ?></span>!
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Welcome back. Your current access level is <?= ucfirst($role) ?>.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($role === 'gate'): ?>
                    <a href="/views/students/index" class="action-card border-primary dark:border-primary-light">
                        <h3 class="font-bold text-xl mb-1">Registered Students</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and manage all students registered in the system.</p>
                    </a>
                    <a href="/views/properties/index" class="action-card border-green-500">
                        <h3 class="font-bold text-xl mb-1">Properties</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add or update student property records and details.</p>
                    </a>
                    <a href="/views/exit_requests/pending" class="action-card border-red-500">
                        <h3 class="font-bold text-xl mb-1">Pending Exits</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Verify and process dormitory exit requests submitted.</p>
                    </a>
                <?php else: ?>
                    <a href="/views/exit_requests/create" class="action-card border-blue-500 col-span-full">
                        <h3 class="font-bold text-xl mb-1">Create Exit Request</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Search students and submit new property exit requests for approval.</p>
                    </a>
                <?php endif; ?>
            </div>

        </main>

        <footer class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm border-t dark:border-gray-700 mt-auto">
            © <?= date('Y') ?> Student Property Management System
        </footer>

    </div>
</div>

<script>
    // --- Sidebar Toggle Logic ---
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            // Prevent background scrolling when sidebar is open
            document.body.style.overflow = 'hidden'; 
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    // --- Dark Mode / Theme Toggle Logic ---
    const themeToggleBtn = document.getElementById('theme-toggle');
    const lightIcon = document.getElementById('theme-toggle-light-icon');
    const darkIcon = document.getElementById('theme-toggle-dark-icon');
    
    // 1. Initial Load: Check localStorage or default to system preference
    const storedTheme = localStorage.getItem('color-theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    const applyTheme = (isDark) => {
        if (isDark) {
            document.documentElement.classList.add('dark');
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            lightIcon.classList.remove('hidden');
            darkIcon.classList.add('hidden');
        }
    }
    // Apply initial theme
    const isDark = storedTheme === 'dark' || (!storedTheme && systemPrefersDark);
    applyTheme(isDark);


    // 2. Event Listener for Toggle
    themeToggleBtn.addEventListener('click', function() {
        const currentThemeIsDark = document.documentElement.classList.contains('dark');
        
        if (currentThemeIsDark) {
            // Switch to light
            localStorage.setItem('color-theme', 'light');
            applyTheme(false);
        } else {
            // Switch to dark
            localStorage.setItem('color-theme', 'dark');
            applyTheme(true);
        }
    });
</script>

<style>
    /* Styling for regular navigation links */
    .nav-link {
        display: flex; /* Added flex to align icon/text */
        align-items: center;
        padding: 0.5rem 1.5rem;
        margin: 0 0.75rem;
        border-radius: 0.5rem;
        color: #d1d5db; /* Gray-300 */
        transition: background-color 0.2s, color 0.2s;
    }
    .nav-link:hover {
        background: #374151; /* Gray-700 */
        color: white;
    }
    /* Styling for the interactive content cards */
    .action-card {
        display: block;
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        border-left-width: 0.25rem; /* For colored accent line */
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .action-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    /* Dark mode adjustments for cards */
    .dark .action-card {
        background: #1f2937; /* Gray-800 */
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .dark .action-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
</style>

</body>
</html> 

