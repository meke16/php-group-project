<?php
session_start();
require_once DIR . '/../config/db.php';
require_once DIR . '/../controllers/AuthController.php';

if (!AuthController::checkAuth()) {
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

        <?php include 'sidebar.php' ?>

        <div class="flex-1 flex flex-col overflow-y-auto">


            <?php include 'header.php' ?>

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
                        <a href="students" class="action-card border-primary dark:border-primary-light">
                            <h3 class="font-bold text-xl mb-1">Registered Students</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage all students registered in the system.</p>
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
            <?php include 'footer.php' ?>



        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
}
