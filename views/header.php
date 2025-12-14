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