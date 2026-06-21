<header class="fixed top-0 left-0 right-0 z-40 transition-all duration-300 w-full" :class="stickyNav ? 'bg-white/90 dark:bg-dark-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/60 py-3 shadow-lg shadow-slate-200/50 dark:shadow-brand-950/20' : 'bg-transparent py-5'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            
            <!-- Logo -->
            <a href="we-welcome-you" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-brand-500/30 transform group-hover:rotate-6 transition-all duration-300">
                    DH
                </div>
                <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">
                    Dev<span class="text-brand-500">Hire</span>
                </span>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="we-welcome-you" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white transition-colors">Home</a>
                <a href="talents" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white transition-colors">Explore Talents</a>
                <a href="#pricing" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white transition-colors">Pricing</a>
                <a href="aboutus" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white transition-colors">About</a>
            </nav>

            <!-- Desktop Action Buttons -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Theme Toggle Button -->
                <button @click="toggleTheme()" class="text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors focus:outline-none p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Toggle Dark Mode">
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!isDark" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <a href="login" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white px-4 py-2 rounded-lg transition-colors">
                    Log In
                </a>
                <a href="register" class="text-sm font-semibold text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 px-5 py-2.5 rounded-xl shadow-md shadow-brand-600/20 transition-all duration-300 hover:-translate-y-0.5">
                    Join Platform
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-2">
                <button @click="toggleTheme()" class="text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors focus:outline-none p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Toggle Dark Mode">
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!isDark" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white focus:outline-none p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Navigation Menu Dropdown -->
    <div x-show="mobileMenuOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-2xl absolute w-full left-0 py-4 px-4 space-y-3"
            style="display: none;">
        <a href="we-welcome-you" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white py-2 px-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Home</a>
        <a href="talents" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white py-2 px-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Explore Talents</a>
        <a href="#pricing" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white py-2 px-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Pricing</a>
        <a href="aboutus" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white py-2 px-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">About</a>
        <hr class="border-slate-200 dark:border-slate-800">
        <div class="flex flex-col gap-2 pt-2">
            <a href="login"  class="w-full text-center text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-white py-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Log In
            </a>
            <a href="signup" class="w-full text-center text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 py-2.5 rounded-lg transition-colors">
                Join Platform
            </a>
        </div>
    </div>
</header>