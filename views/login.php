<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
}

$rememberedUsername = $_COOKIE['remember_username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body class="min-h-screen flex items-center justify-center bg-[#0B1E39] p-4">

<div class="w-full max-w-md bg-white rounded-xl shadow-xl p-8">

    <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-lock text-blue-600 text-2xl"></i>
        </div>
        <h1 class="mt-4 text-2xl font-bold text-gray-700">Sign In</h1>
        <p class="text-gray-500 text-sm">Access your campus management account</p>
    </div>

    <!-- Error message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded">
            <i class="fa fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <!-- Username -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Username</label>
            <div class="relative">
                <input
                    type="text"
                    name="username"
                    required
                    value="<?= htmlspecialchars($rememberedUsername) ?>"
                    class="w-full pl-10 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-600 outline-none"
                    placeholder="Enter username"
                >
                <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full pl-10 pr-10 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-600 outline-none"
                    placeholder="Enter password"
                >
                <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                    <i id="eyeIcon" class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition"
        >
            Sign In
        </button>

    </form>

    <p class="text-center text-xs text-gray-500 mt-6">
        © <?= date('Y') ?> Campus Property Management System
    </p>

</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
</script>

</body>
</html>