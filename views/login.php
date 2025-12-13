<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Redirect to login if not authenticated
if (!AuthController::checkAuth()) {
    header("Location: /views/login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Property Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Sidebar -->
<div class="flex h-screen">
    <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            PMS
        </div>
        <nav class="mt-6">
            <a href="/dashboard.php" class="block py-2 px-6 hover:bg-gray-700 <?= ($_SERVER['REQUEST_URI'] === '/dashboard.php') ? 'bg-gray-700' : '' ?>">Dashboard</a>

            <?php if($role === 'gate'): ?>
                <a href="/views/students/create.php" class="block py-2 px-6 hover:bg-gray-700">Register Student</a>
                <a href="/views/properties/add.php" class="block py-2 px-6 hover:bg-gray-700">Add Property</a>
                <a href="/views/exit_requests/pending.php" class="block py-2 px-6 hover:bg-gray-700">Pending Exits</a>
            <?php elseif($role === 'dormitory'): ?>
                <a href="/views/exit_requests/create.php" class="block py-2 px-6 hover:bg-gray-700">Create Exit Request</a>
            <?php endif; ?>

            <a href="/controllers/AuthController.php?action=logout" class="block py-2 px-6 hover:bg-gray-700 mt-6">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- Header -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">Welcome, <?= htmlspecialchars($username) ?></h1>
            <span class="text-gray-600">Role: <?= ucfirst($role) ?></span>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Example cards -->
                <?php if($role === 'gate'): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="font-semibold text-lg mb-2">Registered Students</h2>
                        <p class="text-gray-600">View and manage all students registered.</p>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="font-semibold text-lg mb-2">Properties</h2>
                        <p class="text-gray-600">Add or update student properties.</p>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="font-semibold text-lg mb-2">Exit Requests</h2>
                        <p class="text-gray-600">Verify dormitory exit requests.</p>
                    </div>
                <?php elseif($role === 'dormitory'): ?>
                    <div class="bg-white p-4 rounded shadow col-span-1 md:col-span-3">
                        <h2 class="font-semibold text-lg mb-2">Create Exit Requests</h2>
                        <p class="text-gray-600">Search students and submit exit requests for approval.</p>
                    </div>
                <?php endif; ?>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow px-6 py-4 text-center text-gray-600">
            &copy; <?= date('Y') ?> Student Property Management System
        </footer>
    </div>
</div>

</body>
</html>