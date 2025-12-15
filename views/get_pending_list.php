<?php

$pageTitle = "Pending Exit Requests";
$requests = $requests ?? [];

function formatDate($date) {
    return date('M d, Y H:i A', strtotime($date));
}
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
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>

<body class="h-full bg-gray-100 dark:bg-gray-800">

<div class="flex h-full">

    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64
                  bg-gray-800 dark:bg-gray-900 text-white
                  flex flex-col h-screen">

        <div class="px-6 py-5 text-2xl font-extrabold text-indigo-400 border-b border-gray-700">
            PMS
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <a href="/dashboard"
               class="flex items-center px-4 py-2 rounded-lg
                      hover:bg-gray-700 transition">
                <i class="fas fa-home w-5 mr-3"></i>
                Dashboard
            </a>

            <a href="/exit/pending"
               class="flex items-center px-4 py-2 rounded-lg
                      bg-indigo-600 text-white shadow">
                <i class="fas fa-clipboard-list w-5 mr-3"></i>
                Pending Requests
            </a>

        </nav>

        <div class="p-4 border-t border-gray-700">
            <a href="/logout"
               class="flex items-center justify-center gap-2 py-2 rounded-lg
                      bg-red-600 hover:bg-red-700 transition">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white dark:bg-gray-900 shadow px-8 py-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <?= $pageTitle ?>
            </h1>
        </header>

        <main class="flex-1 overflow-y-auto p-8">

            <div id="message-box"
                 class="hidden mb-4 p-4 rounded-lg text-sm border"></div>

            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden">

                <?php if (isset($errorMessage)): ?>
                    <div class="p-4 bg-red-100 text-red-700">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>

                <?php elseif (empty($requests)): ?>
                    <div class="p-8 text-center text-gray-500 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                        <p>No pending exit requests.</p>
                    </div>

                <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                                    Request #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                                    Student ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                                    Student Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                                    Submitted
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    #<?= $request['id'] ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    <?= htmlspecialchars($request['student_id']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    <?= htmlspecialchars($request['full_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    <?= formatDate($request['request_date']) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/exit/show?id=<?= $request['id'] ?>"
                                       class="inline-flex items-center gap-1
                                              text-indigo-600 dark:text-indigo-400
                                              hover:underline">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

</body>
</html>
