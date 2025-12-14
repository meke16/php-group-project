<aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gray-800 dark:bg-gray-900 text-white
                  flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

        <div class="p-6 text-2xl font-extrabold text-primary border-b border-gray-700 dark:border-gray-800">
            PMS
        </div>

        <nav class="mt-4 space-y-2 flex-grow">
            <a href="/dashboard"
               class="flex items-center py-2 px-6 mx-3 rounded-lg <?= ($_SERVER['REQUEST_URI'] === '/dashboard') ? 'bg-primary text-white shadow-md' : 'nav-link' ?>">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-7-7m7 7v10a1 1 0 00-1 1h-3m-7 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"></path></svg>
               Dashboard
            </a>

            <?php if ($role === 'gate'): ?>
                <a href="/students" class="flex items-center py-2 px-6 mx-3 rounded-lg nav-link">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Register Student
                </a>
                 <a href="/pending" class="flex items-center py-2 px-6 mx-3 rounded-lg nav-link">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Pending Exits
                </a>
            <?php elseif ($role === 'dormitory'): ?>
                <a href="/views/exit_requests/create" class="flex items-center py-2 px-6 mx-3 rounded-lg nav-link">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Create Exit Request
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-6 border-t border-gray-700 dark:border-gray-800">
            <a href="logout"
               class="flex items-center justify-center py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3v-4a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>