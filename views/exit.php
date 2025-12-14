<?php
require_once dirname(__DIR__) . '/config/db.php';

$pdo =  Database::connect();

function getStudentDetailsAndProperties(PDO $db, string $studentId): ?array {
    $sqlStudent = "SELECT id, student_id, full_name, department, block, room FROM students WHERE student_id = ?";
    $stmtStudent = $db->prepare($sqlStudent);
    $stmtStudent->execute([$studentId]);
    $student = $stmtStudent->fetch();

    if (!$student) return null;

    $sqlProps = "SELECT 
                    sp.category_id, 
                    pc.name AS category_name,
                    SUM(sp.quantity) AS total_quantity
                FROM student_properties sp
                JOIN property_categories pc ON sp.category_id = pc.id
                WHERE sp.student_id = ?
                GROUP BY sp.category_id, pc.name";
    $stmtProps = $db->prepare($sqlProps);
    $stmtProps->execute([$student['id']]);
    $properties = $stmtProps->fetchAll();

    return ['student' => $student, 'owned_properties' => $properties];
}

function isQuantityAvailable(PDO $db, int $studentIdPk, int $categoryId, int $requestedQuantity): bool {
    $sql = "SELECT SUM(quantity) FROM student_properties WHERE student_id = ? AND category_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$studentIdPk, $categoryId]);
    $totalOwned = (int)$stmt->fetchColumn();
    return $totalOwned >= $requestedQuantity;
}

function getCategoryName(PDO $db, int $categoryId): string {
    $sql = "SELECT name FROM property_categories WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$categoryId]);
    return $stmt->fetchColumn() ?: 'Unknown Category';
}



$studentData = null;
$errorMessage = null;
$successMessage = null;

$action = $_POST['action'] ?? null;

if (isset($_GET['success'])) {
    $successMessage = $_GET['success'];
} elseif (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
}


if ($action === 'search') {
    $studentId = trim($_POST['student_id'] ?? '');

    if (empty($studentId)) {
        $errorMessage = "Student ID is required.";
    } else {
        $data = getStudentDetailsAndProperties($pdo, $studentId);
        
        if (!$data) {
            $errorMessage = "Student with ID '{$studentId}' not found.";
        } elseif (empty($data['owned_properties'])) {
            $errorMessage = "Student found, but has no registered properties. Request creation disabled (Business Rule #2).";
        } else {
            $studentData = $data;
        }
    }
    
} elseif ($action === 'submit_request') {
    
    $studentIdPk = (int)($_POST['student_pk'] ?? 0);
    $categories = $_POST['category_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (!$studentIdPk || empty($categories)) {
        header("Location: exit/create?error=" . urlencode("Missing student ID or no items added. (Business Rule #2)"));
        exit;
    }
    
    $pdo->beginTransaction();

    try {
        $sqlRequest = "INSERT INTO exit_requests (student_id, status, request_date) VALUES (?, 'pending', NOW())";
        $stmtRequest = $pdo->prepare($sqlRequest);
        $stmtRequest->execute([$studentIdPk]);
        $requestId = (int)$pdo->lastInsertId();

        if (count($categories) !== count($quantities)) {
             throw new Exception("Form data corrupted: Category and quantity lists mismatch.");
        }

        foreach ($categories as $index => $categoryId) {
            $categoryId = (int)$categoryId;
            $quantity = (int)($quantities[$index] ?? 0);
            
            if ($categoryId <= 0 || $quantity <= 0) continue;

            if (!isQuantityAvailable($pdo, $studentIdPk, $categoryId, $quantity)) {
                $categoryName = getCategoryName($pdo, $categoryId);
                throw new Exception("Validation failed: Requested quantity ({$quantity}) for {$categoryName} exceeds available quantity.");
            }

            $sqlItem = "INSERT INTO exit_request_items (exit_request_id, category_id, quantity) VALUES (?, ?, ?)";
            $stmtItem = $pdo->prepare($sqlItem);
            $stmtItem->execute([$requestId, $categoryId, $quantity]);
        }
        
        $pdo->commit();
        
        header("Location: /exit/create?success=" . urlencode("Exit request #{$requestId} submitted successfully. Status: PENDING."));
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: /exit/create?error=" . urlencode("Submission failed: " . $e->getMessage()));
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory - Create Exit Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Hide the initial placeholder if JS is active */
        #exit-items-list:empty:after {
            content: 'Add items using the button below.';
            display: block;
            text-align: center;
            color: #9ca3af; /* gray-400 */
            padding: 1rem;
        }
    </style>
</head>
<body class="bg-white">

<div class="flex min-h-screen">

    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-2xl font-bold text-indigo-500">Dormitory PMS</h2>
        </div>

        <nav class="flex-1 mt-6 space-y-2">
            <a href="/dashboard"
               class="flex items-center px-6 py-3 mx-3 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>

            <a href="/exit/create"
               class="flex items-center px-6 py-3 mx-3 rounded-lg bg-indigo-600 shadow">
                <i class="fas fa-edit mr-3"></i> Create Exit Request
            </a>
        </nav>

        <div class="p-6 border-t border-gray-700">
            <a href="/logout"
               class="flex items-center justify-center py-2 bg-red-600 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-8 border-b pb-4">Create Exit Request</h1>

        <?php if ($successMessage): ?>
            <div class="p-4 mb-4 rounded-lg bg-green-100 text-green-700 border border-green-300" role="alert">
                <p class="font-bold"><i class="fas fa-check-circle mr-2"></i> Success!</p>
                <p><?= htmlspecialchars($successMessage) ?></p>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="p-4 mb-4 rounded-lg bg-red-100 text-red-700 border border-red-300" role="alert">
                <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Error!</p>
                <p><?= htmlspecialchars($errorMessage) ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-700 shadow-xl rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">1. Find Student Profile</h2>
            <form action="/exit/create" method="POST" class="flex space-x-4">
                <input type="hidden" name="action" value="search">
                <input type="text" name="student_id" placeholder="Enter Student ID (e.g., S12345)" required
                       value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </form>
        </div>

        <?php if ($studentData): 
            $student = $studentData['student'];
            $ownedProperties = $studentData['owned_properties'];
        ?>
            <div id="request-creation-container">
                <div id="student-detail-card" class="bg-white dark:bg-gray-700 shadow-xl rounded-lg p-6 mb-8">
                    <div class="flex items-center space-x-6 mb-6 border-b pb-3">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($student['full_name']) ?></div>
                        <div class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-semibold"><?= htmlspecialchars($student['student_id']) ?></div>
                    </div>
                    
                    <h3 class="text-lg font-semibold mt-4 mb-3 border-t pt-4 dark:text-white dark:border-gray-600">Registered Properties (Max for Exit)</h3>
                    <ul id="owned-list" class="bg-gray-50 dark:bg-gray-800 rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($ownedProperties as $prop): ?>
                            <li class="p-2 border-b last:border-b-0 flex justify-between">
                                <span class="font-medium"><?= htmlspecialchars($prop['category_name']) ?></span> 
                                <span class="text-indigo-600 font-bold" data-max-qty="<?= $prop['total_quantity'] ?>" data-category-id="<?= $prop['category_id'] ?>">
                                    <?= htmlspecialchars($prop['total_quantity']) ?> unit(s)
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">2. Items to Take Out</h2>
                
                <form action="/exit/create" method="POST" id="exit-request-form" class="bg-white dark:bg-gray-700 shadow-xl rounded-lg p-6">
                    <input type="hidden" name="action" value="submit_request">
                    <input type="hidden" name="student_pk" value="<?= $student['id'] ?>">
                    
                    <div id="exit-items-list" class="space-y-4 mb-6 p-4 border rounded-lg border-dashed border-gray-300 dark:border-gray-600">
                        </div>

                    <div class="flex justify-between items-center">
                        <button type="button" id="add-exit-item-btn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md transition duration-150">
                            <i class="fas fa-plus mr-1"></i> Add Item for Exit
                        </button>
                        <button type="submit" id="submit-exit-request-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-150">
                            <i class="fas fa-paper-plane mr-1"></i> Submit Exit Request
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('exit-request-form');
            const itemList = document.getElementById('exit-items-list');
            const addButton = document.getElementById('add-exit-item-btn');
            const ownedList = document.getElementById('owned-list');
            
            const ownedPropertiesMap = Array.from(ownedList ? ownedList.querySelectorAll('span[data-category-id]') : []).reduce((map, span) => {
                const categoryId = span.getAttribute('data-category-id');
                map[categoryId] = {
                    name: span.previousElementSibling.textContent.trim(),
                    maxQty: parseInt(span.getAttribute('data-max-qty'))
                };
                return map;
            }, {});

            let counter = 0;

            function createPropertyRow() {
                counter++;
                let optionsHtml = '<option value="" data-max="0" selected disabled>-- Select Item Category --</option>';
                
                for (const id in ownedPropertiesMap) {
                    const prop = ownedPropertiesMap[id];
                    optionsHtml += `<option value="${id}" data-max="${prop.maxQty}">${prop.name} (Owned: ${prop.maxQty})</option>`;
                }

                const row = document.createElement('div');
                row.className = 'exit-property-row property-row flex flex-wrap gap-4 items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm';
                row.id = `exit-row-${counter}`;
                row.innerHTML = `
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Item Category *</label>
                        <select name="category_id[]" class="item-category mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white shadow-sm p-2" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    
                    <div class="w-20">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Qty *</label>
                        <input type="number" name="quantity[]" class="item-quantity mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white shadow-sm p-2" min="1" value="1" required>
                    </div>

                    <div class="w-16 flex items-end">
                        <button type="button" class="remove-item-btn bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="quantity-feedback w-full text-red-500 text-xs font-medium mt-1 p-1 hidden"></div>
                `;
                return row;
            }


            if (addButton) {
                addButton.addEventListener('click', function() {
                    if (Object.keys(ownedPropertiesMap).length === 0) {
                        alert("Cannot add items: The student has no registered properties.");
                        return;
                    }
                    itemList.appendChild(createPropertyRow());
                });
            }

            itemList?.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    e.target.closest('.exit-property-row').remove();
                }
            });

            itemList?.addEventListener('change', function(e) {
                const target = e.target;
                if (target.classList.contains('item-category') || target.classList.contains('item-quantity')) {
                    const row = target.closest('.exit-property-row');
                    const select = row.querySelector('.item-category');
                    const input = row.querySelector('.item-quantity');
                    const feedback = row.querySelector('.quantity-feedback');

                    const selectedOption = select.options[select.selectedIndex];
                    const maxQuantity = parseInt(selectedOption.getAttribute('data-max') || 0);
                    const currentQuantity = parseInt(input.value || 0);

                    feedback.classList.add('hidden');
                    input.classList.remove('border-red-500');

                    if (currentQuantity > maxQuantity) {
                        feedback.textContent = `Error: Quantity (${currentQuantity}) exceeds owned (${maxQuantity}).`;
                        feedback.classList.remove('hidden');
                        input.classList.add('border-red-500');
                    } else if (currentQuantity <= 0) {
                         feedback.textContent = `Error: Quantity must be 1 or more.`;
                        feedback.classList.remove('hidden');
                        input.classList.add('border-red-500');
                    }
                }
            });

            form?.addEventListener('submit', function(e) {
                let isValid = true;
                const rows = itemList.querySelectorAll('.exit-property-row');

                if (rows.length === 0) {
                    alert("Please add at least one item to the exit request.");
                    e.preventDefault();
                    return;
                }

                rows.forEach(row => {
                    const select = row.querySelector('.item-category');
                    const input = row.querySelector('.item-quantity');
                    const maxQuantity = parseInt(select.options[select.selectedIndex].getAttribute('data-max') || 0);
                    const currentQuantity = parseInt(input.value || 0);
                    
                    if (!select.value || currentQuantity <= 0 || currentQuantity > maxQuantity) {
                        isValid = false;
                        input.dispatchEvent(new Event('change')); 
                    }
                });

                if (!isValid) {
                    alert("Please fix the validation errors before submitting.");
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>