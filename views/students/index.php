<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!AuthController::checkAuth()) {
    header("Location: /login");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$user_initial = strtoupper(substr($username, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Registration & Properties</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        'primary-600': '#4338ca',
                        'muted': '#6b7280'
                    },
                    boxShadow: {
                        'card': '0 6px 18px rgba(15, 23, 42, 0.08)',
                        'card-dark': '0 6px 18px rgba(2,6,23,0.6)'
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .modal {
            transition: opacity 0.18s ease, transform 0.18s ease;
            opacity: 0;
            pointer-events: none;
        }

        .modal.flex {
            opacity: 1;
            pointer-events: auto;
        }

        .property-row {
            transition: all 0.12s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased">

    <div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <div class="flex h-screen overflow-hidden">

        <aside id="sidebar"
            class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gray-800 dark:bg-gray-900 text-white
                  flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

            <div class="p-6 text-2xl font-extrabold text-primary border-b border-gray-700 dark:border-gray-800">
                PMS
            </div>

            <nav class="mt-4 space-y-2 flex-grow">
                <a href="/dashboard"
                    class="flex items-center py-2 px-6 mx-3 rounded-lg <?= ($_SERVER['REQUEST_URI'] === '/dashboard') ? 'bg-primary text-white shadow-md' : 'nav-link' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-7-7m7 7v10a1 1 0 00-1 1h-3m-7 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <?php if ($role === 'gate'): ?>
                    <a href="/students" class="flex items-center py-2 px-6 mx-3 rounded-lg nav-link">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Register Student
                    </a>
                    <a href="/exit/pending"
                        class="flex items-center py-2 px-6 mx-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/exit/pending') !== false) ? 'bg-primary text-white shadow-md' : 'nav-link' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Pending Exit Requests
                    </a>
                <?php elseif ($role === 'dormitory'): ?>
                    <a href="/exit/create"
                        class="flex items-center py-2 px-6 mx-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/exit/create') !== false) ? 'bg-primary text-white shadow-md' : 'nav-link' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Create Exit Request
                    </a>
                <?php endif; ?>
            </nav>

            <div class="p-6 border-t border-gray-700 dark:border-gray-800">
                <a href="logout"
                    class="flex items-center justify-center py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3v-4a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </div>
        </aside>
        <div class="flex-1 flex flex-col overflow-y-auto">

            <header class="sticky top-0 z-20 bg-white dark:bg-gray-800 shadow px-4 py-3 flex justify-between items-center transition-colors duration-300">

                <button onclick="toggleSidebar()" class="lg:hidden text-gray-700 dark:text-gray-200 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>

                <h1 class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">Dashboard</h1>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:hidden">PMS</h1>

                <div class="flex items-center gap-4">

                    <button id="theme-toggle" type="button"
                        class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <svg id="theme-toggle-light-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
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
            <main class="flex-1 p-8 w-full mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-gray-100">Student Management Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Register students and assign assets—clean, consistent, and professional interface.</p>
                </div>

                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <button id="add-student-btn" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg shadow-card transition">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add New Student</span>
                        </button>
                        <button id="open-add-category-modal-btn" class="hidden md:inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded-lg transition">
                            <i class="fas fa-folder-plus text-primary"></i>
                            <span class="text-sm">Add New Property Category</span>
                        </button>
                    </div>

                    <form id="search-form" class="w-full md:w-1/3">
                        <div class="relative">
                            <input type="search" id="search-input" placeholder="Search by Student ID" class="w-full block rounded-lg border border-gray-200 bg-white py-2 pl-3 pr-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary shadow-sm" />
                            <button type="submit" class="absolute right-1 top-1.5 bottom-1 inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white text-sm font-semibold py-1.5 px-3 rounded-lg">
                                <i class="fas fa-search"></i>
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <div id="main-message-box" class="hidden p-3 mb-4 rounded-md border"></div>

                <section id="main-content" class="space-y-6">

                    <section id="student-list-container" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Latest Registered Students</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Showing recent registrations</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card dark:shadow-card-dark overflow-hidden border border-gray-100 dark:border-gray-700">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="students-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php
                                        if (!empty($students)):
                                            foreach ($students as $student): ?>
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($student['full_name']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary"><?= htmlspecialchars($student['student_id']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?= htmlspecialchars($student['department']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?= htmlspecialchars($student['batch']) ?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                                        <button data-id="<?= $student['id'] ?>" class="view-info-btn text-primary hover:text-primary-600 transition text-sm">View</button>
                                                        <button data-id="<?= $student['id'] ?>" class="edit-btn text-indigo-600 hover:text-indigo-400 transition text-sm">Edit</button>
                                                        <button data-id="<?= $student['id'] ?>" class="delete-btn text-red-600 hover:text-red-400 transition text-sm">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students registered yet.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <div id="student-detail-container" style="display:none;"></div>
                </section>
            </main>

            <?php include BASE_PATH . '/views/footer.php'; ?>
        </div>
    </div>

    <!-- Registration Modal -->
    <div id="registration-modal" class="modal hidden fixed inset-0 z-50 items-center justify-center">
        <div class="max-w-3xl w-full mx-4 bg-white dark:bg-gray-800 rounded-xl shadow-card dark:shadow-card-dark 
            overflow-hidden transform transition-all
            max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
                <h3 id="registration-modal-title" class="text-lg md:text-xl font-semibold text-gray-800 dark:text-gray-100">Register New Student</h3>
                <button type="button" class="close-modal text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl leading-none">&times;</button>
            </div>

            <div id="modal-message-box" class="hidden p-4 text-sm"></div>

            <form id="student-registration-form" enctype="multipart/form-data"
                class="p-6 space-y-6 overflow-y-auto">
                <input type="hidden" name="id" id="student-form-id" value="" />
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-700 dark:text-gray-100 mb-3">Personal & Contact</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Full Name *</label>
                            <input type="text" name="full_name" required class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Student ID *</label>
                            <input type="text" name="student_id" required class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Batch</label>
                            <input type="text" name="batch" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Department</label>
                            <input type="text" name="department" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Block</label>
                            <input type="text" name="block" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Room</label>
                            <input type="text" name="room" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Profile Photo</label>
                            <input type="file" name="profile_photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400" />
                            <div id="profile-photo-preview" class="mt-3 hidden">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Current photo:</p>
                                <img id="profile-preview-img" src="" alt="Profile Preview" class="w-24 h-24 object-cover rounded-full shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-md font-semibold text-gray-700 dark:text-gray-100">Property Assignment</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Add one or more properties</div>
                    </div>

                    <div id="properties-list" class="space-y-4"></div>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="button" id="add-property-btn" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-semibold py-2 px-3 rounded-lg transition text-sm">
                            + Add Property
                        </button>
                        <button type="button" class="open-add-category-modal-btn" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 py-2 px-3 rounded-lg text-sm">
                            ⊕ Add New Category
                        </button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="reg-submit-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-6 rounded-lg shadow-card transition">
                        Register Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="add-category-modal" class="modal hidden fixed inset-0 z-50 items-center justify-center">
        <div class="max-w-sm w-full mx-4 bg-white dark:bg-gray-800 rounded-lg shadow-card dark:shadow-card-dark overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Add Property Category</h3>
                <button type="button" class="close-modal text-gray-500 hover:text-gray-700 dark:text-gray-300 text-2xl leading-none">&times;</button>
            </div>

            <div id="category-message-box" class="hidden p-4 text-sm"></div>

            <form id="add-category-form" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Category Name *</label>
                    <input type="text" id="new_category_name" name="category_name" required class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm text-gray-800 dark:text-gray-100" />
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="requires_detail" name="requires_detail" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" />
                    <label for="requires_detail" class="ml-2 text-sm text-gray-600 dark:text-gray-300">Requires Detail (e.g., Model/S/N)</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let propertyCategories = [];
            let propertyRowCounter = 0;
            let editMode = false;
            let editingStudentId = null;

            function showMessage(message, type = 'danger', target = '#main-message-box') {
                const box = $(target);
                box.removeClass('bg-red-50 text-red-700 border-red-200 bg-green-50 text-green-700 border-green-200')
                    .addClass(type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200')
                    .html(`<p class="font-medium">${message}</p>`)
                    .slideDown(180);
                if (target === '#main-message-box') {
                    $('html, body').animate({
                        scrollTop: box.offset().top - 20
                    }, 300);
                }
            }

            function clearMessage(target = '#main-message-box') {
                $(target).slideUp(180).empty();
            }

            function populateCategorySelect(selectElement, selectedId = null) {
                selectElement.empty().append('<option value="" disabled selected>-- Select Property --</option>');
                $.each(propertyCategories, function(i, category) {
                    const selected = (selectedId == category.id) ? 'selected' : '';
                    selectElement.append(`<option value="${category.id}" data-requires-detail="${category.requires_detail}" ${selected}>${category.name}</option>`);
                });
            }

            function createPropertyRow() {
                propertyRowCounter++;
                const html = `
                <div class="property-row flex flex-wrap gap-4 p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0" id="row-${propertyRowCounter}">
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Category</label>
                        <select class="property-category mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm" required></select>
                    </div>
                    <div class="w-28">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Quantity</label>
                        <input type="number" class="property-quantity mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm" min="1" value="1" required>
                    </div>
                    <div class="w-16 flex items-end">
                        <button type="button" class="remove-btn bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded text-sm">Remove</button>
                    </div>
                    <div class="laptop-details w-full p-3 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-md" style="display:none;">
                        <h4 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-100">Item Details</h4>
                        <div class="flex flex-col md:flex-row md:space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Model</label>
                                <input type="text" class="laptop-model mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm" placeholder="Model (e.g., Dell XPS 15)">
                            </div>
                            <div class="flex-1 mt-3 md:mt-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Serial Number</label>
                                <input type="text" class="laptop-serial mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 px-3 text-sm" placeholder="Serial Number">
                            </div>
                        </div>
                    </div>
                </div>
            `;
                return html;
            }

            function loadCategories(callback) {
                $.ajax({
                    url: 'categories/load',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            propertyCategories = response.data;
                            $('.property-category').each(function() {
                                populateCategorySelect($(this));
                            });
                            if (callback) callback();
                        } else {
                            showMessage('Failed to load categories: ' + response.message);
                        }
                    },
                    error: function() {
                        showMessage('Failed to load categories from server.');
                    }
                });
            }

            function setCreateMode() {
                editMode = false;
                editingStudentId = null;
                $('#student-form-id').val('');
                $('#registration-modal-title').text('Register New Student');
                $('#reg-submit-btn').text('Register Student');
                $('#profile-photo-preview').hide();
                $('#profile-preview-img').attr('src', '');
                $('#student-registration-form')[0].reset();
                $('#properties-list').empty();
                // ensure at least one property row exists when opening
            }

            function openCreateModal() {
                setCreateMode();
                if ($('#properties-list').is(':empty')) {
                    $('#properties-list').append(createPropertyRow());
                    populateCategorySelect($('#properties-list .property-category:last'));
                }
                $('#registration-modal').removeClass('hidden').addClass('flex');
            }

            function openEditModal(id) {
                clearMessage('#modal-message-box');
                $.ajax({
                    url: 'students/edit',
                    type: 'GET',
                    dataType: 'json',
                    data: { id: id },
                    success: function(res) {
                        if (!res.success) {
                            showMessage(res.message || 'Failed to load student', 'danger');
                            return;
                        }

                        const student = res.data.student;
                        const properties = res.data.properties || [];

                        editMode = true;
                        editingStudentId = id;
                        $('#student-form-id').val(id);
                        $('#registration-modal-title').text('Edit Student');
                        $('#reg-submit-btn').text('Update Student');

                        // populate fields
                        const form = $('#student-registration-form')[0];
                        form.full_name.value = student.full_name || '';
                        form.student_id.value = student.student_id || '';
                        form.batch.value = student.batch || '';
                        form.department.value = student.department || '';
                        form.block.value = student.block || '';
                        form.room.value = student.room || '';

                        // show current profile photo preview if available
                        if (student.profile_photo) {
                            $('#profile-preview-img').attr('src', student.profile_photo);
                            $('#profile-photo-preview').show();
                        } else {
                            $('#profile-photo-preview').hide();
                            $('#profile-preview-img').attr('src', '');
                        }

                        // build property rows
                        $('#properties-list').empty();
                        if (properties.length === 0) {
                            $('#properties-list').append(createPropertyRow());
                            populateCategorySelect($('#properties-list .property-category:last'));
                        } else {
                            properties.forEach(function(p) {
                                const newRow = $(createPropertyRow());
                                $('#properties-list').append(newRow);
                                populateCategorySelect(newRow.find('.property-category'));
                                // set values after a short delay (options available)
                                setTimeout(function() {
                                    newRow.find('.property-category').val(p.category_id).trigger('change');
                                    newRow.find('.property-quantity').val(p.quantity || 1);
                                    newRow.find('.laptop-model').val(p.model || '');
                                    newRow.find('.laptop-serial').val(p.serial_number || '');
                                }, 30);
                            });
                        }

                        $('#registration-modal').removeClass('hidden').addClass('flex');
                        clearMessage('#modal-message-box');
                    },
                    error: function(xhr) {
                        let message = 'Failed to load student';
                        try { message = JSON.parse(xhr.responseText).message || message; } catch(e) {}
                        showMessage(message, 'danger');
                    }
                });
            }

            loadCategories(function() {
                $('#add-student-btn').on('click', function() {
                    openCreateModal();
                });
            });

            $('#add-property-btn').on('click', function() {
                const newRow = $(createPropertyRow());
                $('#properties-list').append(newRow);
                populateCategorySelect(newRow.find('.property-category'));
            });

            $('#properties-list').on('click', '.remove-btn', function() {
                $(this).closest('.property-row').slideUp(120, function() {
                    $(this).remove();
                });
            });

            $('#properties-list').on('change', '.property-category', function() {
                const select = $(this);
                const requiresDetail = parseInt(select.find('option:selected').data('requires-detail'));
                const detailsDiv = select.closest('.property-row').find('.laptop-details');
                const modelInput = detailsDiv.find('.laptop-model');
                const serialInput = detailsDiv.find('.laptop-serial');

                if (requiresDetail === 1) {
                    detailsDiv.slideDown(160);
                    modelInput.prop('required', true);
                    serialInput.prop('required', true);
                } else {
                    detailsDiv.slideUp(160, function() {
                        modelInput.val('').prop('required', false);
                        serialInput.val('').prop('required', false);
                    });
                }
            });

            $('.close-modal').on('click', function() {
                $(this).closest('.modal').addClass('hidden').removeClass('flex');
                if ($(this).closest('#registration-modal').length) {
                    setCreateMode();
                    clearMessage('#modal-message-box');
                }
            });

            $('#open-add-category-modal-btn').on('click', function() {
                $('#add-category-modal').removeClass('hidden').addClass('flex');
                clearMessage('#category-message-box');
            });
            $('.open-add-category-modal-btn').on('click', function() {
                $('#add-category-modal').removeClass('hidden').addClass('flex');
                clearMessage('#category-message-box');
            });

            $('#add-category-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');

                submitBtn.prop('disabled', true).text('Saving...');
                clearMessage('#category-message-box');

                $.ajax({
                    url: 'categories/store',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success', '#category-message-box');

                            propertyCategories.push(response.new_category);
                            propertyCategories.sort((a, b) => a.name.localeCompare(b.name));

                            $('.property-category').each(function() {
                                populateCategorySelect($(this), response.new_category.id);
                            });

                            form[0].reset();
                            setTimeout(() => {
                                $('#add-category-modal').addClass('hidden').removeClass('flex');
                            }, 900);
                        } else {
                            showMessage(response.message, 'danger', '#category-message-box');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Save Category');
                    }
                });
            });

            // Submit handler supports both create and update
            $('#student-registration-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                clearMessage('#modal-message-box');

                const form = $(this);
                const submitBtn = $('#reg-submit-btn');

                const propertiesData = [];
                let isValid = true;

                $('.property-row').each(function() {
                    const row = $(this);
                    const categorySelect = row.find('.property-category');
                    const categoryId = categorySelect.val();
                    const quantity = row.find('.property-quantity').val();
                    const requiresDetail = parseInt(categorySelect.find('option:selected').data('requires-detail'));
                    const model = row.find('.laptop-model').val();
                    const serial = row.find('.laptop-serial').val();

                    if (!categoryId || quantity < 1) {
                        isValid = false;
                        showMessage("Please select a property and ensure quantity is 1 or more for all rows.", 'danger', '#modal-message-box');
                        return false;
                    }

                    if (requiresDetail === 1 && (!model || !serial)) {
                        isValid = false;
                        showMessage("Item Model and Serial Number are required for selected categories.", 'danger', '#modal-message-box');
                        return false;
                    }

                    propertiesData.push({
                        category_id: categoryId,
                        quantity: quantity,
                        requires_detail: requiresDetail,
                        model: model,
                        serial_number: serial
                    });
                });

                if (!isValid) return;

                const formData = new FormData(this);
                formData.append('properties_data', JSON.stringify(propertiesData));

                submitBtn.prop('disabled', true).text(editMode ? 'Updating...' : 'Registering...');

                const url = editMode ? 'students/update' : 'students/store';

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');

                            setTimeout(() => {
                                $('#registration-modal').addClass('hidden').removeClass('flex');
                                loadStudentList();
                                setCreateMode();
                            }, 700);
                        } else {
                            showMessage(response.message, 'danger', '#modal-message-box');
                        }
                    },
                    error: function(xhr) {
                        let message = 'An unknown error occurred.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            message = response.message || message;
                        } catch (e) {}
                        showMessage(`Error: ${message}`, 'danger', '#modal-message-box');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(editMode ? 'Update Student' : 'Register Student');
                    }
                });
            });

            function loadStudentList() {
                $.ajax({
                    url: 'students',
                    type: 'GET',
                    dataType: 'html',
                    success: function(html) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableBody = $(doc).find('#students-tbody').html();

                        if (newTableBody) {
                            $('#students-tbody').html(newTableBody);
                        }

                        $('#student-list-container').show();
                        $('#student-detail-container').hide().empty();
                        clearMessage();
                    },
                    error: function() {
                        showMessage("Error refreshing student list.");
                    }
                });
            }

            function loadStudentDetail(id, isStudentId = false) {
                const url = isStudentId ? `students/search?student_id=${id}` : `students/show?id=${id}`;
                clearMessage();

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#student-detail-container').html(buildStudentDetailView(response.data)).show();
                            $('#student-list-container').hide();
                        } else {
                            showMessage(response.message);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Failed to load student details.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            msg = response.message || msg;
                        } catch (e) {}
                        showMessage(msg, 'danger');
                    }
                });
            }

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                const studentId = $('#search-input').val().trim();
                if (studentId) {
                    loadStudentDetail(studentId, true);
                } else {
                    loadStudentList();
                }
            });

            // View details (delegated)
            $('#main-content').on('click', '.view-info-btn', function() {
                const studentPk = $(this).data('id');
                loadStudentDetail(studentPk, false);
            });

            // Edit (delegated)
            $('#main-content').on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                openEditModal(id);
            });

            // Delete (delegated)
            $('#main-content').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) return;

                $.ajax({
                    url: 'students/delete',
                    type: 'POST',
                    dataType: 'json',
                    data: { id: id },
                    success: function(res) {
                        if (res.success) {
                            showMessage(res.message, 'success');
                            loadStudentList();
                        } else {
                            showMessage(res.message || 'Failed to delete', 'danger');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to delete student';
                        try { message = JSON.parse(xhr.responseText).message || message; } catch(e) {}
                        showMessage(message, 'danger');
                    }
                });
            });

            function buildStudentDetailView(student) {
                let propsHtml = '';
                (student.properties || []).forEach(prop => {
                    let detailHtml = '';
                    if (prop.details) {
                        detailHtml = `<ul class="text-xs ml-4 list-disc text-gray-600 dark:text-gray-300">
                        <li>Model: ${prop.details.model}</li>
                        <li>S/N: ${prop.details.serial_number}</li>
                    </ul>`;
                    }
                    propsHtml += `
                    <li class="py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="font-semibold text-gray-800 dark:text-gray-100">${prop.category_name}</span>: ${prop.quantity} unit(s)
                        ${detailHtml}
                    </li>`;
                });

                return `
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-card dark:shadow-card-dark">
                    <button class="back-to-list-btn bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-100 font-semibold py-2 px-4 rounded text-sm mb-4">
                        ← Back to List
                    </button>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Student Details: ${student.full_name}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-center">
                                <img src="${student.profile_photo ? student.profile_photo : 'https://via.placeholder.com/150'}" alt="Profile Photo" class="w-28 h-28 object-cover mx-auto rounded-full shadow-md mb-3">
                                <p class="text-lg font-semibold text-primary">${student.student_id}</p>
                                <p class="text-sm text-muted dark:text-gray-400">${student.department}</p>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-3 text-gray-700 dark:text-gray-100">Personal Info</h3>
                            <div class="space-y-2 text-gray-600 dark:text-gray-300">
                                <p><strong>Batch:</strong> ${student.batch || 'N/A'}</p>
                                <p><strong>Department:</strong> ${student.department || 'N/A'}</p>
                                <p><strong>Block:</strong> ${student.block || 'N/A'}</p>
                                <p><strong>Room:</strong> ${student.room || 'N/A'}</p>
                                <p><strong>Registered:</strong> ${new Date(student.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-3 text-gray-700 dark:text-gray-100">Assigned Properties</h3>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                ${propsHtml || '<li class="text-gray-500 dark:text-gray-400">No properties assigned.</li>'}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            }

            $('#main-content').on('click', '.back-to-list-btn', function() {
                loadStudentList();
                $('#search-input').val('');
            });
        });

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

        const themeToggleBtn = document.getElementById('theme-toggle');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');

        const storedTheme = localStorage.getItem('color-theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        const applyTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                if (darkIcon) darkIcon.classList.remove('hidden');
                if (lightIcon) lightIcon.classList.add('hidden');
            } else {
                document.documentElement.classList.remove('dark');
                if (lightIcon) lightIcon.classList.remove('hidden');
                if (darkIcon) darkIcon.classList.add('hidden');
            }
        }
        const isDark = storedTheme === 'dark' || (!storedTheme && systemPrefersDark);
        applyTheme(isDark);

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const currentThemeIsDark = document.documentElement.classList.contains('dark');

                if (currentThemeIsDark) {
                    localStorage.setItem('color-theme', 'light');
                    applyTheme(false);
                } else {
                    localStorage.setItem('color-theme', 'dark');
                    applyTheme(true);
                }
            });
        }
    </script>
</body>

</html>