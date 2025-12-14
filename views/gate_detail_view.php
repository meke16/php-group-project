<?php

$pageTitle = "Verify Exit Request #{$request['id']}";
$request = $request ?? [];
$items = $items ?? [];
$keptItems = $keptItems ?? [];

function formatDate($date) {
    return date('M d, Y H:i A', strtotime($date));
}

function getStatusBadge($status) {
    return match ($status) {
        'pending'  => 'bg-yellow-100 text-yellow-800',
        'checked'  => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        default    => 'bg-gray-100 text-gray-800',
    };
}

$isReadOnly = $request['status'] !== 'pending';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS - <?= $pageTitle ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>

<body class="h-full bg-gray-100 dark:bg-gray-800">
<div class="flex h-full">

    <aside class="fixed lg:static inset-y-0 left-0 w-64 bg-gray-800 dark:bg-gray-900 text-white flex flex-col h-screen">

        <div class="px-6 py-5 text-2xl font-extrabold text-indigo-400 border-b border-gray-700">
            PMS
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="/dashboard"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-home w-5 mr-3"></i> Dashboard
            </a>

            <a href="/exit/pending"
               class="flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white shadow">
                <i class="fas fa-clipboard-list w-5 mr-3"></i> Pending Exit Requests
            </a>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <a href="/logout"
               class="flex items-center justify-center gap-2 py-2 rounded-lg bg-red-600 hover:bg-red-700">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white dark:bg-gray-900 shadow px-8 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <?= $pageTitle ?>
            </h1>
            <span class="px-4 py-1 text-sm font-semibold rounded-full <?= getStatusBadge($request['status']) ?>">
                <?= strtoupper($request['status']) ?>
            </span>
        </header>

        <main class="flex-1 overflow-y-auto p-8">

            <a href="/exit/pending"
               class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 mb-6">
                <i class="fas fa-arrow-left"></i> Back to Pending List
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="bg-white dark:bg-gray-700 rounded-xl shadow p-6 h-fit">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 border-b pb-2">
                        Student Information
                    </h2>

                    <div class="space-y-3 text-gray-600 dark:text-gray-300">
                        <p><strong>Name:</strong> <?= htmlspecialchars($request['full_name']) ?></p>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($request['student_id']) ?></p>
                        <p><strong>Department:</strong> <?= htmlspecialchars($request['department']) ?></p>
                        <p><strong>Hostel:</strong> <?= htmlspecialchars($request['block']) ?></p>
                        <p><strong>Room:</strong> <?= htmlspecialchars($request['room']) ?></p>
                        <p class="pt-2 border-t dark:border-gray-600">
                            <strong>Request Date:</strong> <?= formatDate($request['request_date']) ?>
                        </p>
                    </div>
                </div>

                <!-- Exit Items -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-700 rounded-xl shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 border-b pb-2">
                        <i class="fas fa-box-open text-red-500 mr-2"></i>
                        Items to Exit Campus
                    </h2>

                    <?php if (empty($items)): ?>
                        <p class="text-gray-500 dark:text-gray-300 text-center py-6">
                            No items listed for this request.
                        </p>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Item Category
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                        Quantity
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                <?php foreach ($items as $item): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            <?= htmlspecialchars($item['category_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-right text-red-600">
                                            <?= (int)$item['quantity'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
