<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devhire | Premium Digital Talent Marketplace</title>
    <!-- Google Fonts: Inter for crisp, modern tech typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for lightweight interactive components (Tabs, Sliders, Modals, Mobile Menu) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Tailwind Configuration for Custom Animations & Gradients -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                            950: '#1e1b4b'
                        },
                        dark: {
                            900: '#0b0f19',
                            950: '#030712'
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 8s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            '0%, 100%': { opacity: 0.6 },
                            '50%': { opacity: 0.9 },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Smooth Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0f19;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
        /* Custom dynamic backdrop styling */
        .glassmorphism {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glassmorphism-light {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body x-data="{ 
    mobileMenuOpen: false, 
    stickyNav: false,
    activeWorkTab: 'talents',
    talentFilter: 'all',
    jobFilter: 'all',
    billingPeriod: 'monthly',
    notification: { show: false, message: '', type: 'success' },
    showHireModal: false,
    selectedTalentName: '',
    showApplyModal: false,
    selectedJobTitle: '',
    showAuthModal: false,
    authType: 'login',
    
    showToast(msg, type = 'success') {
        this.notification.message = msg;
        this.notification.type = type;
        this.notification.show = true;
        setTimeout(() => { this.notification.show = false; }, 4000);
    }
}" 
@scroll.window="stickyNav = (window.pageYOffset > 20) ? true : false"
class="bg-dark-950 text-slate-100 font-sans antialiased overflow-x-hidden">

    <!-- Toast Notification System -->
    <div x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-5 right-5 z-50 max-w-sm w-full bg-slate-900 border border-slate-800 shadow-2xl rounded-xl pointer-events-auto overflow-hidden"
         style="display: none;">
        <div class="p-4 flex items-center gap-3">
            <template x-if="notification.type === 'success'">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </template>
            <template x-if="notification.type === 'info'">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </template>
            <div class="flex-1">
                <p class="text-sm font-semibold text-white" x-text="notification.message"></p>
            </div>
            <button @click="notification.show = false" class="text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Header/Navbar Section -->
    <?php include_once 'header.php' ?>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-24 md:pt-44 md:pb-36 overflow-hidden">
        <!-- Colorful Radial Background Glows -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-900/20 rounded-full filter blur-[120px] pointer-events-none animate-glow"></div>
        <div class="absolute top-[20%] right-[-10%] w-[45%] h-[45%] bg-purple-900/20 rounded-full filter blur-[120px] pointer-events-none animate-glow" style="animation-delay: 3s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Content: Value Proposition -->
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    <!-- High-Converting Announcement Badge -->
                    <div class="inline-flex items-center gap-2 bg-slate-900/80 border border-slate-800 hover:border-slate-700/80 transition-all px-3 py-1.5 rounded-full text-xs font-semibold text-brand-300">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Trusted by top global tech companies
                    </div>

                    <!-- Core Headline -->
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-none">
                        Hire elite digital <br>
                        <span class="bg-gradient-to-r from-slate-400 via-indigo-400 to-purple-800 bg-clip-text text-transparent">
                            talent on demand
                        </span>
                    </h1>

                    <!-- Detailed Subheadline -->
                    <p class="text-lg text-slate-400 max-w-2xl mx-auto lg:mx-0">
                        Devhire bridges the gap between pre-vetted digital professionals and hyper-growth teams. Hire developers, designers, writers, and growth experts for contract, freelance, or remote full-time positions instantly.
                    </p>

                    <!-- Call To Action Buttons with glowing premium effect -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#talents" class="group relative px-8 py-4 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 rounded-xl font-semibold text-white shadow-xl shadow-brand-500/20 hover:shadow-brand-500/30 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <span>Hire Top Talent</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="#jobs" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-xl font-semibold text-slate-200 hover:text-white transition-all flex items-center justify-center gap-2">
                            Join Platform
                        </a>
                    </div>

                    <!-- Live Market Status Info -->
                    <div class="pt-4 flex flex-wrap gap-x-8 gap-y-3 justify-center lg:justify-start text-xs text-slate-400 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pre-vetted professionals
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Zero upfront hiring fees
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Contracts completed in 48h
                        </div>
                    </div>
                </div>

                <!-- Right Content: Interactive Dashboard Mockup -->
                <div class="lg:col-span-5 relative">
                    <!-- Glow Backdrop -->
                    <div class="absolute inset-0 bg-brand-500/10 rounded-[32px] filter blur-xl transform translate-x-3 translate-y-3 pointer-events-none"></div>
                    
                    <!-- Dashboard Card Container -->
                    <div class="glassmorphism rounded-[24px] p-6 shadow-2xl relative border border-slate-800/80 animate-float">
                        <!-- Dashboard Top bar -->
                        <div class="flex items-center justify-between pb-6 border-b border-slate-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
                                <span class="text-xs text-slate-400 ml-2 font-mono">dashboard_preview.v1</span>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 bg-brand-500/20 text-brand-300 rounded-md">Live Platform</span>
                        </div>

                        <!-- Active Talents Mock Tracker -->
                        <div class="space-y-4 pt-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-slate-300">Fast Match Pipeline</h3>
                                <span class="text-xs text-brand-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                                    12 matches live today
                                </span>
                            </div>

                            <!-- Mock Match Row 1 -->
                            <div class="bg-slate-900/80 rounded-xl p-3 border border-slate-800 hover:border-slate-700/80 transition-all flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop" 
                                             alt="Sarah" class="w-10 h-10 rounded-full object-cover border-2 border-brand-500/40">
                                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-dark-950 rounded-full"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">Sarah Jenkins</p>
                                        <p class="text-[10px] text-slate-400">Senior UI/UX • $85/hr</p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-medium bg-emerald-500/10 text-emerald-400 px-2 py-1 rounded">Active Interview</span>
                            </div>

                            <!-- Mock Match Row 2 -->
                            <div class="bg-slate-900/80 rounded-xl p-3 border border-slate-800 hover:border-slate-700/80 transition-all flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&auto=format&fit=crop" 
                                             alt="Alex" class="w-10 h-10 rounded-full object-cover border-2 border-purple-500/40">
                                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-dark-950 rounded-full"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">Alex Rivera</p>
                                        <p class="text-[10px] text-slate-400">Full Stack Engineer • $110/hr</p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-medium bg-brand-500/10 text-brand-400 px-2 py-1 rounded">Matched (98%)</span>
                            </div>

                            <!-- Financial Mini-Insight -->
                            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 mt-2">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-[11px] font-medium text-slate-400">Average Hiring Velocity</span>
                                    <span class="text-xs font-bold text-emerald-400">+14% Growth</span>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl font-bold text-white">48 Hours</span>
                                    <span class="text-[10px] text-slate-400">versus industry 30-day average</span>
                                </div>
                                <!-- Mock bar chart element -->
                                <div class="mt-3 flex items-end gap-1.5 h-12 pt-2">
                                    <div class="bg-slate-800 w-full h-[85%] rounded-sm"></div>
                                    <div class="bg-slate-800 w-full h-[60%] rounded-sm"></div>
                                    <div class="bg-slate-800 w-full h-[75%] rounded-sm"></div>
                                    <div class="bg-brand-500/40 w-full h-[40%] rounded-sm"></div>
                                    <div class="bg-gradient-to-t from-brand-600 to-indigo-500 w-full h-full rounded-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating decorative UI component 1 -->
                    <div class="absolute -bottom-8 -left-10 bg-slate-900 border border-slate-800 rounded-xl p-3.5 shadow-xl hidden sm:flex items-center gap-3">
                        <div class="w-9 h-9 bg-brand-500/10 rounded-lg flex items-center justify-center text-brand-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400">Escrow Secure</p>
                            <p class="text-xs font-bold text-white">Payment Protected</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Statistics / Counter Bar Section -->
    <section class="py-12 bg-dark-900 border-y border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center divide-y lg:divide-y-0 lg:divide-x divide-slate-800/80">
                <div class="pt-6 lg:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">15,000+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase mt-2 tracking-wider">Active Tech Talents</p>
                </div>
                <div class="pt-6 lg:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">2,400+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase mt-2 tracking-wider">Vetted Startups</p>
                </div>
                <div class="pt-6 lg:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">98.4%</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase mt-2 tracking-wider">Hiring Success Rate</p>
                </div>
                <div class="pt-6 lg:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-brand-400 tracking-tight">$45M+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase mt-2 tracking-wider">Talent Earnings</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By / Logo Wall Section -->
    <section class="py-16 bg-dark-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-bold text-slate-500 uppercase tracking-widest mb-10">We enable hiring teams from the world's best innovators</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 items-center justify-items-center opacity-60">
                
                <!-- Logo 1 (Stripe Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20 8.69V5.01L12 1L4 5.01v3.68c0 5.17 3.39 10.03 8 11.31 4.61-1.28 8-6.14 8-11.31z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Stripe</span>
                </div>
                
                <!-- Logo 2 (Figma Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8.5 2C10.43 2 12 3.57 12 5.5V18.5C12 20.43 10.43 22 8.5 22S5 20.43 5 18.5 6.57 15 8.5 15H12V9H8.5C6.57 9 5 7.43 5 5.5S6.57 2 8.5 2z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Figma</span>
                </div>

                <!-- Logo 3 (Slack Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523 2.528 2.528 0 0 1-2.522-2.523 2.528 2.528 0 0 1 2.522-2.52h2.52v2.52zm1.261 0a2.528 2.528 0 0 1 2.52-2.52h5.043a2.528 2.528 0 0 1 2.522 2.52v5.042a2.528 2.528 0 0 1-2.522 2.52H8.824a2.528 2.528 0 0 1-2.52-2.52v-5.042z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Slack</span>
                </div>

                <!-- Logo 4 (Airbnb Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Airbnb</span>
                </div>

                <!-- Logo 5 (Vercel Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Vercel</span>
                </div>

                <!-- Logo 6 (Linear Mock) -->
                <div class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v4h2v-4h3V9h-3V6h-2v3H8v2h3z"/></svg>
                    <span class="font-bold tracking-tight text-lg">Linear</span>
                </div>

            </div>
        </div>
    </section>

    <!-- Featured Talent Categories Section -->
    <section id="categories" class="py-24 bg-dark-900 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-dark-950 via-dark-900 to-dark-950 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Browse talents by expertise</h2>
                <p class="text-slate-400 mt-4 text-base sm:text-lg">Explore verified industry leaders specializing in high-growth startup tasks, digital scaling, and software engineering.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Category 1: Web Developers -->
                <div @click="talentFilter = 'dev'; showToast('Filtered talented professionals: Developers Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-6 group-hover:bg-brand-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-brand-300 transition-colors">Web Developers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">React, Node.js, Next.js, Django, Ruby, and custom APIs.</p>
                    <span class="text-xs font-semibold text-brand-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Developers
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 2: Mobile Developers -->
                <div @click="talentFilter = 'dev'; showToast('Filtered talented professionals: Mobile Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center mb-6 group-hover:bg-violet-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-violet-300 transition-colors">Mobile Developers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">iOS, Android, Swift, Kotlin, Flutter, and React Native.</p>
                    <span class="text-xs font-semibold text-violet-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Devs
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 3: UI/UX Designers -->
                <div @click="talentFilter = 'design'; showToast('Filtered talented professionals: Design Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center mb-6 group-hover:bg-pink-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-pink-300 transition-colors">UI/UX Designers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Product wireframing, high-fi prototypes, Figma, and research.</p>
                    <span class="text-xs font-semibold text-pink-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Designers
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 4: Graphic Designers -->
                <div @click="talentFilter = 'design'; showToast('Filtered talented professionals: Design Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">Graphic Designers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Branding systems, illustrations, visual decks, and layout art.</p>
                    <span class="text-xs font-semibold text-amber-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Graphics
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 5: Video Editors -->
                <div @click="talentFilter = 'marketing'; showToast('Filtered talented professionals: Marketing/Media Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center mb-6 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l5.553-2.776A1 1 0 0122 8.143v7.714a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-rose-300 transition-colors">Video Editors</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">SaaS ads, long-form content, post-production, VFX, 3D motion design.</p>
                    <span class="text-xs font-semibold text-rose-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Editors
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 6: Digital Marketers -->
                <div @click="talentFilter = 'marketing'; showToast('Filtered talented professionals: Marketing Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-emerald-300 transition-colors">Digital Marketers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">SEO, Pay-per-click Ads, Brand strategy, Growth hacking funnel.</p>
                    <span class="text-xs font-semibold text-emerald-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Marketers
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 7: Blockchain Developers -->
                <div @click="talentFilter = 'dev'; showToast('Filtered talented professionals: Blockchain Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-6 group-hover:bg-cyan-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">Web3 & Blockchain</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Solidity, Rust, smart contracts, dApp systems, and protocol auditing.</p>
                    <span class="text-xs font-semibold text-cyan-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Web3
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

                <!-- Category 8: Content Writers -->
                <div @click="talentFilter = 'writer'; showToast('Filtered talented professionals: Writers Section', 'info')" 
                     class="group bg-slate-900/40 hover:bg-slate-900 border border-slate-800/80 hover:border-brand-500/50 p-6 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-950/10 cursor-pointer">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-orange-300 transition-colors">Content Writers</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Technical documentation, SEO copywriting, brand copy, whitepapers.</p>
                    <span class="text-xs font-semibold text-orange-400 group-hover:underline inline-flex items-center gap-1">
                        Explore Writers
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>

            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 bg-dark-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Streamlined Process</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">How Devhire Works</h2>
                <p class="text-slate-400 mt-4 text-base sm:text-lg">Two specialized tracks optimized for immediate integration.</p>
                
                <!-- Intersect Tabs -->
                <div class="inline-flex p-1 bg-slate-900 border border-slate-800 rounded-xl mt-8">
                    <button @click="activeWorkTab = 'talents'" 
                            :class="activeWorkTab === 'talents' ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                        For Talents
                    </button>
                    <button @click="activeWorkTab = 'companies'" 
                            :class="activeWorkTab === 'companies' ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                        For Companies
                    </button>
                </div>
            </div>

            <!-- TAB 1: For Talents -->
            <div x-show="activeWorkTab === 'talents'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Step 1 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">01</div>
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">1. Create Profile</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Build a comprehensive professional profile showcasing your historical accomplishments, digital core assets, and code repositories.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">02</div>
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">2. Showcase Skills</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Submit to verification tests. Showcase live designs, product strategies, or smart contracts verified by real engineering reviews.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">03</div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">3. Get Hired</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Receive job notifications matched by our intelligent algorithm. Interview with high-performance startups and settle contracts securely.</p>
                </div>

            </div>

            <!-- TAB 2: For Companies -->
            <div x-show="activeWorkTab === 'companies'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 md:grid-cols-3 gap-8"
                 style="display: none;">
                
                <!-- Step 1 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">01</div>
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">1. Post Jobs</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Outline your specific business objectives, tech stack, duration parameters, budget rates, and essential required qualifications.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">02</div>
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">2. Review Applicants</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Inspect customized profile matches. Evaluate coding tests, visual prototype screens, and portfolio metrics directly on platform.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-900/30 border border-slate-800/80 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700/80 transition-all">
                    <div class="absolute -top-6 -right-6 text-7xl font-black text-slate-800/30 select-none group-hover:scale-110 group-hover:text-brand-500/10 transition-all">03</div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">3. Hire Professionals</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Initiate contract, approve secure escrow payments, and coordinate project syncs with automatic payroll processing built-in.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- Featured Talents Section with LIVE Interactive Alpine.js filtering -->
    <section id="talents" class="py-24 bg-dark-900 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Exclusive Network</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Hire Verified Innovators</h2>
                    <p class="text-slate-400 mt-2 text-sm sm:text-base">Review highly rated experts from our elite global pool of tech talent.</p>
                </div>

                <!-- Live Filters -->
                <div class="flex flex-wrap gap-2">
                    <button @click="talentFilter = 'all'" 
                            :class="talentFilter === 'all' ? 'bg-brand-600 border-brand-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        All Talents
                    </button>
                    <button @click="talentFilter = 'dev'" 
                            :class="talentFilter === 'dev' ? 'bg-brand-600 border-brand-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        Developers
                    </button>
                    <button @click="talentFilter = 'design'" 
                            :class="talentFilter === 'design' ? 'bg-brand-600 border-brand-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        Designers
                    </button>
                    <button @click="talentFilter = 'marketing'" 
                            :class="talentFilter === 'marketing' ? 'bg-brand-600 border-brand-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        Marketing/Media
                    </button>
                </div>
            </div>

            <!-- Profile Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Talent Card 1: Senior Developer -->
                <div x-show="talentFilter === 'all' || talentFilter === 'dev'"
                     x-transition:enter="transition ease-out duration-300"
                     class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-brand-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Head info -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold bg-brand-500/10 text-brand-300 px-2.5 py-1 rounded-full">Developer</span>
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-bold text-amber-400 flex items-center">⭐ 4.9</span>
                                <span class="text-slate-500 text-[10px]">(64 reviews)</span>
                            </div>
                        </div>

                        <!-- Bio / Avatar -->
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=256&auto=format&fit=crop" 
                                 alt="Marcus" class="w-14 h-14 rounded-full object-cover border-2 border-slate-800">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-1.5">
                                    Marcus Thorne
                                    <!-- Verified Badge -->
                                    <span class="text-brand-400" title="Vetted Professional">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    </span>
                                </h3>
                                <p class="text-xs text-slate-400">Next.js & Rust Expert</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            Senior software systems engineer with 8+ years specializing in complex API design, Rust contract optimization, and serverless frontend architectures.
                        </p>

                        <!-- Skill Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Next.js</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Rust</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Web3</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">GraphQL</span>
                        </div>
                    </div>

                    <!-- Footer / Rate & CTA -->
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Hourly Rate</p>
                            <p class="text-lg font-bold text-white">$95<span class="text-xs text-slate-400">/hr</span></p>
                        </div>
                        <button @click="selectedTalentName = 'Marcus Thorne'; showHireModal = true" 
                                class="text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 px-4 py-2.5 rounded-lg transition-colors">
                            Hire Marcus
                        </button>
                    </div>
                </div>

                <!-- Talent Card 2: Senior UI/UX Designer -->
                <div x-show="talentFilter === 'all' || talentFilter === 'design'"
                     x-transition:enter="transition ease-out duration-300"
                     class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-brand-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Head info -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold bg-pink-500/10 text-pink-300 px-2.5 py-1 rounded-full">Designer</span>
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-bold text-amber-400 flex items-center">⭐ 5.0</span>
                                <span class="text-slate-500 text-[10px]">(112 reviews)</span>
                            </div>
                        </div>

                        <!-- Bio / Avatar -->
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=256&auto=format&fit=crop" 
                                 alt="Elena" class="w-14 h-14 rounded-full object-cover border-2 border-slate-800">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-1.5">
                                    Elena Rostova
                                    <span class="text-brand-400" title="Vetted Professional">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    </span>
                                </h3>
                                <p class="text-xs text-slate-400">Principal UX Architect</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            Ex-Stripe principal UX Architect specializing in conversion-focused dashboard designs, deep branding blueprints, and interactive SaaS application models.
                        </p>

                        <!-- Skill Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Figma</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">UX Research</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Design Systems</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">SaaS</span>
                        </div>
                    </div>

                    <!-- Footer / Rate & CTA -->
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Hourly Rate</p>
                            <p class="text-lg font-bold text-white">$120<span class="text-xs text-slate-400">/hr</span></p>
                        </div>
                        <button @click="selectedTalentName = 'Elena Rostova'; showHireModal = true" 
                                class="text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 px-4 py-2.5 rounded-lg transition-colors">
                            Hire Elena
                        </button>
                    </div>
                </div>

                <!-- Talent Card 3: Senior Digital Marketer -->
                <div x-show="talentFilter === 'all' || talentFilter === 'marketing'"
                     x-transition:enter="transition ease-out duration-300"
                     class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-brand-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Head info -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold bg-emerald-500/10 text-emerald-300 px-2.5 py-1 rounded-full">Marketing</span>
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-bold text-amber-400 flex items-center">⭐ 4.8</span>
                                <span class="text-slate-500 text-[10px]">(48 reviews)</span>
                            </div>
                        </div>

                        <!-- Bio / Avatar -->
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&auto=format&fit=crop" 
                                 alt="David" class="w-14 h-14 rounded-full object-cover border-2 border-slate-800">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-1.5">
                                    David Vance
                                    <span class="text-brand-400" title="Vetted Professional">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    </span>
                                </h3>
                                <p class="text-xs text-slate-400">Growth Hacker & SEO Expert</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            Proven digital scaling expert focused on structured CAC optimization, landing funnel conversion audits, and enterprise-grade SEO.
                        </p>

                        <!-- Skill Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Paid Ads</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Funnel Audits</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">SEO</span>
                            <span class="text-[10px] font-medium bg-slate-800 text-slate-300 px-2.5 py-1 rounded">Analytics</span>
                        </div>
                    </div>

                    <!-- Footer / Rate & CTA -->
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Hourly Rate</p>
                            <p class="text-lg font-bold text-white">$80<span class="text-xs text-slate-400">/hr</span></p>
                        </div>
                        <button @click="selectedTalentName = 'David Vance'; showHireModal = true" 
                                class="text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 px-4 py-2.5 rounded-lg transition-colors">
                            Hire David
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Why Choose Us / Corporate Strengths Section -->
    <section class="py-24 bg-dark-950 relative overflow-hidden">
        <div class="absolute top-[40%] left-[-20%] w-[50%] h-[50%] bg-indigo-900/10 rounded-full filter blur-[150px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Market Advantage</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Built for premium hiring</h2>
                <p class="text-slate-400 mt-4 text-base sm:text-lg">Skip the resume mountains and work directly with top-tier vetted creators.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Benefit 1: Verified Professionals -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Verified Professionals</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Only the top 3% of global digital applicants clear our multi-stage testing including background, portfolio, and code assessments.</p>
                </div>

                <!-- Benefit 2: Secure Payments -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Escrow Protected Security</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Funds are fully protected. Approve payments only when pre-set contractual milestone objectives are approved by your team.</p>
                </div>

                <!-- Benefit 3: Fast Hiring -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">48-Hour Velocity Matching</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Say goodbye to week-long back-and-forth threads. Receive dedicated pre-matched profiles within forty-eight hours of posting.</p>
                </div>

                <!-- Benefit 4: Real-time Chat -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Built-in Real-time Collaboration</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Message, call, sync documents, and construct shared agreements instantly using our state-of-the-art secure workspace dashboard.</p>
                </div>

                <!-- Benefit 5: Portfolio Showcase -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Rich Interactive Case Files</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Review historical deployment systems, user flow maps, and performance metrics inside interactive, rich-text case files.</p>
                </div>

                <!-- Benefit 6: Smart Matchmaking -->
                <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-2xl hover:border-slate-700/80 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Predictive AI Matchmaking</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Our matching system reviews cultural goals, tech systems compatibility, and timezone requirements to find your ideal candidates.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Job Opportunities Board Section -->
    <section id="jobs" class="py-24 bg-dark-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Featured Opportunities</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">High-growth remote jobs</h2>
                    <p class="text-slate-400 mt-2 text-sm sm:text-base">Apply directly to audited startups with immediate hiring budgets.</p>
                </div>

                <!-- Job Filter -->
                <div class="flex flex-wrap gap-2">
                    <button @click="jobFilter = 'all'" 
                            :class="jobFilter === 'all' ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        All Openings
                    </button>
                    <button @click="jobFilter = 'fulltime'" 
                            :class="jobFilter === 'fulltime' ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        Full-Time
                    </button>
                    <button @click="jobFilter = 'contract'" 
                            :class="jobFilter === 'contract' ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold border transition-all">
                        Contract / Gig
                    </button>
                </div>
            </div>

            <!-- Job Listings Cards Container -->
            <div class="space-y-4">
                
                <!-- Job 1 -->
                <div x-show="jobFilter === 'all' || jobFilter === 'fulltime'" 
                     class="bg-slate-900/40 border border-slate-800 hover:border-brand-500/50 p-6 rounded-2xl transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-xl hover:shadow-brand-950/5 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-lg font-black text-indigo-400 flex-shrink-0">
                            L
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">Senior Software Developer (React/Node)</h3>
                                <span class="text-[10px] font-bold bg-brand-500/10 text-brand-400 px-2 py-0.5 rounded-full">Remote</span>
                                <span class="text-[10px] font-bold bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Full-Time</span>
                            </div>
                            <p class="text-sm text-slate-300 mt-1">Linear App • <span class="text-xs text-slate-400">San Francisco / Remote</span></p>
                            <p class="text-xs text-slate-400 mt-2 max-w-2xl">Help rebuild collaborative workflow channels. High degree of product ownership, modern tech stack (NextJS, TypeScript, Postgres, Tailwind).</p>
                        </div>
                    </div>
                    <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-4 border-t md:border-t-0 pt-4 md:pt-0 border-slate-800/80">
                        <div>
                            <p class="text-[10px] text-slate-500 text-left md:text-right">Comp Range</p>
                            <p class="text-sm font-extrabold text-white">$140k - $185k/yr</p>
                        </div>
                        <button @click="selectedJobTitle = 'Senior Software Developer at Linear App'; showApplyModal = true" 
                                class="text-xs font-bold text-white bg-slate-800 group-hover:bg-brand-600 hover:!bg-brand-500 px-5 py-2.5 rounded-lg transition-all">
                            Apply Now
                        </button>
                    </div>
                </div>

                <!-- Job 2 -->
                <div x-show="jobFilter === 'all' || jobFilter === 'contract'" 
                     class="bg-slate-900/40 border border-slate-800 hover:border-brand-500/50 p-6 rounded-2xl transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-xl hover:shadow-brand-950/5 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-lg font-black text-rose-400 flex-shrink-0">
                            F
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">Lead Product Design Contractor</h3>
                                <span class="text-[10px] font-bold bg-brand-500/10 text-brand-400 px-2 py-0.5 rounded-full">Remote</span>
                                <span class="text-[10px] font-bold bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Contract (6m)</span>
                            </div>
                            <p class="text-sm text-slate-300 mt-1">Flowbase Studios • <span class="text-xs text-slate-400">Sydney / Remote</span></p>
                            <p class="text-xs text-slate-400 mt-2 max-w-2xl">Own the redesign of our premium design system files. Working directly with founders and senior engineers. Experience with Figma variables is a must.</p>
                        </div>
                    </div>
                    <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-4 border-t md:border-t-0 pt-4 md:pt-0 border-slate-800/80">
                        <div>
                            <p class="text-[10px] text-slate-500 text-left md:text-right">Comp Range</p>
                            <p class="text-sm font-extrabold text-white">$90 - $115/hr</p>
                        </div>
                        <button @click="selectedJobTitle = 'Lead Product Design Contractor at Flowbase Studios'; showApplyModal = true" 
                                class="text-xs font-bold text-white bg-slate-800 group-hover:bg-brand-600 hover:!bg-brand-500 px-5 py-2.5 rounded-lg transition-all">
                            Apply Now
                        </button>
                    </div>
                </div>

                <!-- Job 3 -->
                <div x-show="jobFilter === 'all' || jobFilter === 'fulltime'" 
                     class="bg-slate-900/40 border border-slate-800 hover:border-brand-500/50 p-6 rounded-2xl transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-xl hover:shadow-brand-950/5 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-lg font-black text-emerald-400 flex-shrink-0">
                            G
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">Senior Growth Marketer</h3>
                                <span class="text-[10px] font-bold bg-brand-500/10 text-brand-400 px-2 py-0.5 rounded-full">Remote</span>
                                <span class="text-[10px] font-bold bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Full-Time</span>
                            </div>
                            <p class="text-sm text-slate-300 mt-1">GlowMetric Tech • <span class="text-xs text-slate-400">London / Remote</span></p>
                            <p class="text-xs text-slate-400 mt-2 max-w-2xl">Execute scale-out campaigns across dynamic paid search formats. Manage deep attribution pipelines and guide long-term brand narrative structure.</p>
                        </div>
                    </div>
                    <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-4 border-t md:border-t-0 pt-4 md:pt-0 border-slate-800/80">
                        <div>
                            <p class="text-[10px] text-slate-500 text-left md:text-right">Comp Range</p>
                            <p class="text-sm font-extrabold text-white">$110k - $140k/yr</p>
                        </div>
                        <button @click="selectedJobTitle = 'Senior Growth Marketer at GlowMetric Tech'; showApplyModal = true" 
                                class="text-xs font-bold text-white bg-slate-800 group-hover:bg-brand-600 hover:!bg-brand-500 px-5 py-2.5 rounded-lg transition-all">
                            Apply Now
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 bg-dark-950 relative overflow-hidden" x-data="{ currentSlide: 0 }">
        <!-- Background light sphere decoration -->
        <div class="absolute bottom-[-20%] right-[-20%] w-[50%] h-[50%] bg-purple-900/10 rounded-full filter blur-[150px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Success Stories</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">What founders & talents say</h2>
                <p class="text-slate-400 mt-4 text-base sm:text-lg">Real feedback from scaling founders and independent digital professionals.</p>
            </div>

            <!-- Testimonials Grid Slider -->
            <div class="relative max-w-4xl mx-auto">
                
                <!-- Slides Container -->
                <div class="overflow-hidden relative rounded-3xl bg-slate-900/50 border border-slate-800/80 p-8 sm:p-12 shadow-2xl">
                    
                    <!-- Slide 1 -->
                    <div x-show="currentSlide === 0" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="space-y-6">
                        <div class="text-2xl sm:text-3xl text-slate-100 italic leading-relaxed font-light">
                            "Finding premium contractors with proven SaaS design track records historically took us weeks. With Devhire, we matched with Elena in under twenty-four hours. She is absolutely outstanding."
                        </div>
                        <div class="flex items-center gap-4 pt-4">
                            <img src="https://images.unsplash.com/photo-1519345182560-3f2917c472ef?q=80&w=256&auto=format&fit=crop" 
                                 alt="Founder avatar" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <p class="text-base font-bold text-white">Jameson Croft</p>
                                <p class="text-xs text-slate-400">Founder & CEO, VelocityApp</p>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div x-show="currentSlide === 1" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="space-y-6"
                         style="display: none;">
                        <div class="text-2xl sm:text-3xl text-slate-100 italic leading-relaxed font-light">
                            "The automated payroll integration, contract transparency, and elite client tier on Devhire are outstanding. I run all my long-term consulting operations and work pipelines through it securely."
                        </div>
                        <div class="flex items-center gap-4 pt-4">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop" 
                                 alt="Talent avatar" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <p class="text-base font-bold text-white">Elena Rostova</p>
                                <p class="text-xs text-slate-400">Independent Lead Product Architect</p>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div x-show="currentSlide === 2" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="space-y-6"
                         style="display: none;">
                        <div class="text-2xl sm:text-3xl text-slate-100 italic leading-relaxed font-light">
                            "No more back-and-forth negotiating budgets or checking credentials. The candidates presented by Devhire matched our tech goals perfectly. Best platform upgrade we ever made."
                        </div>
                        <div class="flex items-center gap-4 pt-4">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&auto=format&fit=crop" 
                                 alt="CTO avatar" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <p class="text-base font-bold text-white">Alistair Vance</p>
                                <p class="text-xs text-slate-400">CTO, CyberFort Systems</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Slider Controls -->
                <div class="flex justify-between items-center mt-8">
                    <div class="flex gap-2">
                        <template x-for="i in [0, 1, 2]">
                            <button @click="currentSlide = i" 
                                    :class="currentSlide === i ? 'bg-brand-500 w-8' : 'bg-slate-800 w-2.5 hover:bg-slate-700'"
                                    class="h-2.5 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                    <div class="flex gap-3">
                        <button @click="currentSlide = (currentSlide === 0) ? 2 : currentSlide - 1" 
                                class="w-10 h-10 rounded-full border border-slate-800 hover:border-slate-700 bg-slate-900/40 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="currentSlide = (currentSlide === 2) ? 0 : currentSlide + 1" 
                                class="w-10 h-10 rounded-full border border-slate-800 hover:border-slate-700 bg-slate-900/40 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-dark-900 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Flexible SaaS Pricing</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Simple plans for any stage</h2>
                <p class="text-slate-400 mt-4 text-base sm:text-lg">Connect with digital talents at structured, affordable fee schedules.</p>
                
                <!-- Monthly/Annual Toggle with savings flag -->
                <div class="inline-flex items-center gap-3 bg-slate-950 p-1 rounded-xl border border-slate-800 mt-8">
                    <button @click="billingPeriod = 'monthly'" 
                            :class="billingPeriod === 'monthly' ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all">
                        Monthly Billing
                    </button>
                    <button @click="billingPeriod = 'yearly'" 
                            :class="billingPeriod === 'yearly' ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all relative">
                        Annual Billing
                        <span class="absolute -top-3.5 right-[-8px] text-[9px] font-bold bg-emerald-500 text-dark-950 px-1.5 py-0.5 rounded-full uppercase tracking-wider scale-95">Save 20%</span>
                    </button>
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                
                <!-- Plan 1: Starter -->
                <div class="bg-slate-900/40 border border-slate-800/80 hover:border-slate-700 rounded-3xl p-8 flex flex-col justify-between hover:shadow-xl transition-all relative">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Starter Tier</span>
                        <h3 class="text-2xl font-bold text-white mt-2">Emerging Teams</h3>
                        <p class="text-xs text-slate-400 mt-2">Ideal for early-stage bootstrapper startups requiring fast gig matching.</p>
                        
                        <div class="my-6">
                            <span class="text-4xl font-black text-white" x-text="billingPeriod === 'monthly' ? '$49' : '$39'">$49</span>
                            <span class="text-xs text-slate-400">/ month</span>
                        </div>

                        <hr class="border-slate-800 my-6">

                        <!-- Features list -->
                        <ul class="space-y-3">
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Up to 3 live job positions
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Basic verified talent applications
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Automated escrow contracts
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Dedicated account representative
                            </li>
                        </ul>
                    </div>

                    <button @click="showToast('Starter plan checkout initialized', 'success')" 
                            class="w-full mt-8 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold rounded-xl transition-all">
                        Get Started
                    </button>
                </div>

                <!-- Plan 2: Professional (Recommended) -->
                <div class="bg-gradient-to-b from-slate-900 to-dark-900 border-2 border-brand-500 rounded-3xl p-8 flex flex-col justify-between hover:shadow-2xl hover:shadow-brand-950/20 transition-all relative">
                    <!-- Recommended Ribbon -->
                    <span class="absolute top-[-14px] left-1/2 transform -translate-x-1/2 text-[10px] font-bold bg-brand-500 text-white px-3 py-1 rounded-full uppercase tracking-wider">Most Popular</span>
                    
                    <div>
                        <span class="text-xs font-bold text-brand-400 uppercase tracking-widest">Growth Tier</span>
                        <h3 class="text-2xl font-bold text-white mt-2">Professional</h3>
                        <p class="text-xs text-slate-400 mt-2">Perfect for growing startups matching with elite full-time creators.</p>
                        
                        <div class="my-6">
                            <span class="text-4xl font-black text-white" x-text="billingPeriod === 'monthly' ? '$149' : '$119'">$149</span>
                            <span class="text-xs text-slate-400">/ month</span>
                        </div>

                        <hr class="border-slate-800/80 my-6">

                        <!-- Features list -->
                        <ul class="space-y-3">
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Unlimited live job positions
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Priority talent matchmaking dashboard
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Advanced technical code review files
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                24/7 Premium customer support
                            </li>
                        </ul>
                    </div>

                    <button @click="showToast('Professional plan checkout initialized', 'success')" 
                            class="w-full mt-8 py-3 bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-brand-500/25 transition-all">
                        Get Started
                    </button>
                </div>

                <!-- Plan 3: Enterprise -->
                <div class="bg-slate-900/40 border border-slate-800/80 hover:border-slate-700 rounded-3xl p-8 flex flex-col justify-between hover:shadow-xl transition-all relative">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Enterprise Tier</span>
                        <h3 class="text-2xl font-bold text-white mt-2">Custom Scaling</h3>
                        <p class="text-xs text-slate-400 mt-2">Custom-tailored parameters for high-performance scale-outs.</p>
                        
                        <div class="my-6 flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">Custom</span>
                        </div>

                        <hr class="border-slate-800 my-6">

                        <!-- Features list -->
                        <ul class="space-y-3">
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                White-glove tailored hiring campaigns
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Dedicated strategic talent managers
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Custom billing and SLA protections
                            </li>
                            <li class="flex items-center gap-2.5 text-xs text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Full custom platform integrations
                            </li>
                        </ul>
                    </div>

                    <button @click="showToast('Enterprise custom demo request sent', 'info')" 
                            class="w-full mt-8 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold rounded-xl transition-all">
                        Contact Enterprise Team
                    </button>
                </div>

            </div>
        </div>
    </section>

    <!-- Call To Action Section -->
    <section class="py-24 bg-dark-950 relative overflow-hidden border-t border-slate-900">
        <!-- Accent circles -->
        <div class="absolute inset-0 bg-brand-500/5 rounded-full filter blur-[100px] pointer-events-none transform scale-75"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="glassmorphism rounded-[32px] p-8 sm:p-16 border border-slate-800 text-center relative overflow-hidden">
                
                <!-- Small ambient light vector -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-[300px] h-[1px] bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

                <h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Start hiring vetted tech <br>
                    talents instantly
                </h2>
                <p class="text-slate-400 text-base sm:text-lg max-w-2xl mx-auto mt-6">
                    Join thousands of high-velocity startups using Devhire to integrate top developers, designers, and marketers on-demand.
                </p>

                <!-- Responsive actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-10">
                    <button @click="showToast('Redirecting to secure signup portal...', 'info')" 
                            class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 rounded-xl font-bold text-white shadow-xl shadow-brand-500/20 transform hover:-translate-y-0.5 transition-all text-sm">
                        Build Startup Team
                    </button>
                    <button @click="showToast('Redirecting to global talent application...', 'info')" 
                            class="w-full sm:w-auto px-8 py-4 bg-slate-900 border border-slate-800 hover:border-slate-750 text-slate-200 hover:text-white rounded-xl font-bold transition-all text-sm">
                        Apply as Talent
                    </button>
                </div>

                <p class="text-[11px] text-slate-500 mt-6 font-mono">Free setup • Zero commitment • Risk-free matches</p>

            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <?php include_once 'footer.html' ?>

    <!-- Interactive Modals Container System -->

    <!-- Modal 1: Hire Talent Quote request -->
    <div x-show="showHireModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/80 backdrop-blur-md" 
         style="display: none;"
         @keydown.escape.window="showHireModal = false">
        <div @click.away="showHireModal = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl relative">
            
            <button @click="showHireModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 class="text-xl font-bold text-white mb-1">Inquire to Hire <span class="text-brand-400" x-text="selectedTalentName"></span></h3>
            <p class="text-xs text-slate-400 mb-6">Briefly submit company expectations to structure an automated invitation profile.</p>

            <form @submit.prevent="showHireModal = false; showToast('Hiring request sent successfully! We\'ll follow up within 24 hours.', 'success')" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Company Name</label>
                    <input type="text" placeholder="Stripe" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Project Scope Summary</label>
                    <textarea placeholder="Outline required objectives, key features and estimated delivery parameters..." rows="3" required
                              class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Hourly Budget (USD)</label>
                    <input type="number" placeholder="85" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                
                <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                    Send Marketplace Proposal
                </button>
            </form>
        </div>
    </div>

    <!-- Modal 2: Easy Job Application Modal -->
    <div x-show="showApplyModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/80 backdrop-blur-md" 
         style="display: none;"
         @keydown.escape.window="showApplyModal = false">
        <div @click.away="showApplyModal = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl relative">
            
            <button @click="showApplyModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 class="text-xl font-bold text-white mb-1">Apply for Position</h3>
            <p class="text-xs text-brand-400 mb-6" x-text="selectedJobTitle"></p>

            <form @submit.prevent="showApplyModal = false; showToast('Application submitted! Our system will complete its match profile review shortly.', 'success')" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name</label>
                    <input type="text" placeholder="Sarah Jenkins" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" placeholder="sarah@designflow.com" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Portfolio or GitHub Link</label>
                    <input type="url" placeholder="https://github.com/myusername" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                
                <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                    Submit Job Application
                </button>
            </form>
        </div>
    </div>


</body>
</html>