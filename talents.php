<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Talents | Devhire Premium Marketplace</title>
    
    <!-- Google Fonts: Inter for crisp, modern tech typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for lightweight interactive components (Tabs, Search, Sliders, Modals) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Tailwind Configuration -->
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
            height: 8px;
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
        /* Glassmorphism utility */
        .glassmorphism {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body x-data="{ 
    mobileMenuOpen: false, 
    stickyNav: true,
    
    /* Toast System */
    notification: { show: false, message: '', type: 'success' },
    showToast(msg, type = 'success') {
        this.notification.message = msg;
        this.notification.type = type;
        this.notification.show = true;
        setTimeout(() => { this.notification.show = false; }, 4000);
    },

    /* Interactive Filters State */
    searchQuery: '',
    selectedCategory: 'all',
    selectedExperience: 'all',
    maxRate: 150,
    onlyAvailable: false,
    sortBy: 'rating',
    mobileFilterOpen: false,

    /* Modals State */
    showHireModal: false,
    selectedTalentName: '',
    showDetailModal: false,
    
    /* Focused Talent Profile Details */
    activeTalent: {},

    /* Complete Talent Database (Pre-filled Vetted Profiles) */
    talents: [
        {
            id: 1,
            name: 'Marcus Thorne',
            category: 'dev',
            role: 'Next.js & Rust Systems Engineer',
            rate: 95,
            rating: 4.9,
            reviews: 64,
            image: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=256&auto=format&fit=crop',
            bio: 'Senior software systems engineer with 8+ years specializing in complex API design, Rust contract optimization, and serverless frontend architectures.',
            skills: ['Next.js', 'Rust', 'Web3', 'GraphQL', 'TypeScript', 'Node.js'],
            experience: 'senior',
            available: true,
            location: 'San Francisco, US',
            projects: [
                { name: 'DeFi Liquidity Router', description: 'Built an optimized smart contract router executing swaps under 45k gas.' },
                { name: 'Enterprise Headless Platform', description: 'Architected next-gen composable UI systems handling 50k requests/min.' }
            ],
            metrics: { completionRate: '100%', hoursLogged: '1,420h', activeContracts: '2' }
        },
        {
            id: 2,
            name: 'Elena Rostova',
            category: 'design',
            role: 'Principal UX Architect & Designer',
            rate: 120,
            rating: 5.0,
            reviews: 112,
            image: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=256&auto=format&fit=crop',
            bio: 'Ex-Stripe principal UX Architect specializing in conversion-focused dashboard designs, deep design systems, and interactive SaaS application interfaces.',
            skills: ['Figma', 'UX Research', 'Design Systems', 'SaaS', 'Prototyping', 'Tailwind'],
            experience: 'expert',
            available: true,
            location: 'Berlin, DE',
            projects: [
                { name: 'Stripe Billing Redesign', description: 'Refactored key payment subscription dashboards resulting in a 4.2% lift in checkout setups.' },
                { name: 'CyberSec Analytics Workspace', description: 'Constructed an dark-mode enterprise visualization tool with nested component files.' }
            ],
            metrics: { completionRate: '98%', hoursLogged: '3,100h', activeContracts: '1' }
        },
        {
            id: 3,
            name: 'David Vance',
            category: 'marketing',
            role: 'Growth Hacker & Paid Acquisition',
            rate: 80,
            rating: 4.8,
            reviews: 48,
            image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&auto=format&fit=crop',
            bio: 'Proven digital scaling expert focused on structured customer acquisition cost (CAC) optimization, landing funnel conversion audits, and enterprise-grade SEO engines.',
            skills: ['Paid Ads', 'Funnel Optimization', 'SEO', 'Google Analytics', 'Marketing Automation'],
            experience: 'senior',
            available: false,
            location: 'London, UK',
            projects: [
                { name: 'SaaS Funnel Audit', description: 'Helped automated HR platform scale recurring revenue by 120% using hyper-targeted digital frameworks.' },
                { name: 'Web3 Launch Campaign', description: 'Orchestrated organic SEO and paid pipeline resulting in 40,000 waitlist entries.' }
            ],
            metrics: { completionRate: '96%', hoursLogged: '920h', activeContracts: '0' }
        },
        {
            id: 4,
            name: 'Aria Chen',
            category: 'dev',
            role: 'iOS Mobile App Lead Developer',
            rate: 110,
            rating: 4.95,
            reviews: 74,
            image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop',
            bio: 'Native iOS expert engineering fast, responsive, and secure mobile platforms. Deep knowledge of Swift, SwiftUI, and local system integration models.',
            skills: ['iOS', 'SwiftUI', 'Swift', 'Objective-C', 'CoreData', 'Combine'],
            experience: 'expert',
            available: true,
            location: 'Toronto, CA',
            projects: [
                { name: 'ZenMind Meditation App', description: 'Created award-winning health application reaching Top 5 in Lifestyle App Store.' },
                { name: 'Fintech Mobile Wallet', description: 'Led Swift architecture team integrating high-performance local biometrics verification protocols.' }
            ],
            metrics: { completionRate: '100%', hoursLogged: '2,240h', activeContracts: '3' }
        },
        {
            id: 5,
            name: 'Kofi Mensah',
            category: 'writer',
            role: 'Technical Writer & SEO Content Strategist',
            rate: 65,
            rating: 4.75,
            reviews: 31,
            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&auto=format&fit=crop',
            bio: 'Specialized technical documentation engineer turning deep engineering schemas into readable SaaS guidebooks, interactive API portals, and landing copy.',
            skills: ['Technical Documentation', 'SEO Copywriting', 'Markdown', 'Git', 'API References', 'Ghost'],
            experience: 'mid',
            available: true,
            location: 'Accra, GH',
            projects: [
                { name: 'Cloud Infra Developer Guide', description: 'Drafted developer workspace books that decreased support tickets by 32%.' },
                { name: 'AI API Documentation Hub', description: 'Restructured responsive Swagger schemas and interactive quickstart guides.' }
            ],
            metrics: { completionRate: '100%', hoursLogged: '610h', activeContracts: '1' }
        },
        {
            id: 6,
            name: 'Nikhil Sharma',
            category: 'dev',
            role: 'Solidity & Smart Contract Developer',
            rate: 135,
            rating: 5.0,
            reviews: 89,
            image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=256&auto=format&fit=crop',
            bio: 'Ethereum virtual machine (EVM) expert writing gas-optimized smart contracts. Certified auditor with comprehensive security auditing background.',
            skills: ['Solidity', 'Rust', 'Hardhat', 'Web3.js', 'Contract Auditing', 'EVM'],
            experience: 'expert',
            available: true,
            location: 'Bangalore, IN',
            projects: [
                { name: 'Cross-chain Yield Vaults', description: 'Engineered safe automated vaults routing yield over 4 major layer-2 ecosystems.' },
                { name: 'NFT Gaming Marketplace', description: 'Created customized ERC-1155 structures matching low-latency transaction processing.' }
            ],
            metrics: { completionRate: '100%', hoursLogged: '1,890h', activeContracts: '4' }
        },
        {
            id: 7,
            name: 'Zoe Katsaros',
            category: 'design',
            role: 'Senior Motion & Graphic Designer',
            rate: 85,
            rating: 4.85,
            reviews: 55,
            image: 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?q=80&w=256&auto=format&fit=crop',
            bio: 'Bringing static brands to life with breathtaking 3D motion systems, high-converting product demo videos, and cohesive visual identities.',
            skills: ['After Effects', 'Cinema 4D', 'Motion Graphics', 'Branding', 'Adobe Suite', 'Figma'],
            experience: 'senior',
            available: false,
            location: 'Athens, GR',
            projects: [
                { name: 'DevOps Hero Explainer Video', description: 'Produced high-fidelity SaaS interface visual guides achieving 1.2M views.' },
                { name: 'Global Rebrand System', description: 'Created typography, vector guides, and guidelines utilized by international teams.' }
            ],
            metrics: { completionRate: '97%', hoursLogged: '1,150h', activeContracts: '0' }
        },
        {
            id: 8,
            name: 'Alexander Lindqvist',
            category: 'marketing',
            role: 'Lead AI Automation & Operations Consultant',
            rate: 140,
            rating: 4.9,
            reviews: 42,
            image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&auto=format&fit=crop',
            bio: 'Structuring intelligent operational frameworks using Large Language Models, LLMOps pipelines, and custom CRM automation nodes to replace manual workloads.',
            skills: ['AI Integrations', 'LLMOps', 'Python', 'Zapier', 'Make.com', 'LangChain'],
            experience: 'expert',
            available: true,
            location: 'Stockholm, SE',
            projects: [
                { name: 'Automated Sales Pipeline', description: 'Designed automated agent workflow converting high-intent leads inside 90 seconds.' },
                { name: 'Customer Support LLM Proxy', description: 'Deployed custom safety guardrails and system instructions over customer queries.' }
            ],
            metrics: { completionRate: '95%', hoursLogged: '840h', activeContracts: '2' }
        }
    ],

    /* Live Filter Pipeline */
    get filteredTalents() {
        return this.talents
            .filter(t => {
                // Search query match (name, role, or skills)
                if (this.searchQuery.trim() !== '') {
                    const query = this.searchQuery.toLowerCase();
                    const nameMatch = t.name.toLowerCase().includes(query);
                    const roleMatch = t.role.toLowerCase().includes(query);
                    const skillMatch = t.skills.some(s => s.toLowerCase().includes(query));
                    if (!nameMatch && !roleMatch && !skillMatch) return false;
                }
                // Category filter
                if (this.selectedCategory !== 'all' && t.category !== this.selectedCategory) {
                    return false;
                }
                // Experience filter
                if (this.selectedExperience !== 'all' && t.experience !== this.selectedExperience) {
                    return false;
                }
                // Max rate filter
                if (t.rate > this.maxRate) {
                    return false;
                }
                // Availability filter
                if (this.onlyAvailable && !t.available) {
                    return false;
                }
                return true;
            })
            .sort((a, b) => {
                // Sorting strategy
                if (this.sortBy === 'rating') {
                    return b.rating - a.rating;
                } else if (this.sortBy === 'rate-asc') {
                    return a.rate - b.rate;
                } else if (this.sortBy === 'rate-desc') {
                    return b.rate - a.rate;
                } else if (this.sortBy === 'reviews') {
                    return b.reviews - a.reviews;
                }
                return 0;
            });
    },

    openDetailModal(talent) {
        this.activeTalent = talent;
        this.showDetailModal = true;
    }
}" 
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

    <!-- Page Sub-Header Section -->
    <section class="relative pt-28 pb-10 bg-dark-950 border-b border-slate-900 overflow-hidden">
        <!-- Background Radial glow -->
        <div class="absolute top-[-50%] left-1/2 -translate-x-1/2 w-[80%] h-[300px] bg-brand-500/15 rounded-full filter blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Talent Discovery Portal</span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Explore Premium Professionals</h1>
                    <p class="text-sm text-slate-400 mt-2 max-w-2xl">Connect with pre-vetted specialists verified through dynamic code tests, live portfolio verification, and structured reference audits.</p>
                </div>
                
                <!-- Quick Metric -->
                <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:min-w-[200px] text-left">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Active Experts Pool</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-white">15,480</span>
                        <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            +32 today
                        </span>
                    </div>
                </div>
            </div>

            <!-- Fast Categories Filter Slider Bar -->
            <div class="mt-8 flex items-center gap-2 overflow-x-auto pb-3 -mx-4 px-4 sm:mx-0 sm:px-0">
                <button @click="selectedCategory = 'all'; showToast('Broadening search parameters...', 'info')"
                        :class="selectedCategory === 'all' ? 'bg-brand-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-200'"
                        class="px-4.5 py-2 rounded-xl text-xs font-semibold border shrink-0 transition-all">
                    🌍 All Domains
                </button>
                <button @click="selectedCategory = 'dev'; showToast('Filtering parameters: Developers...', 'info')"
                        :class="selectedCategory === 'dev' ? 'bg-brand-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-200'"
                        class="px-4.5 py-2 rounded-xl text-xs font-semibold border shrink-0 transition-all">
                    💻 Development &amp; Systems
                </button>
                <button @click="selectedCategory = 'design'; showToast('Filtering parameters: Designers...', 'info')"
                        :class="selectedCategory === 'design' ? 'bg-brand-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-200'"
                        class="px-4.5 py-2 rounded-xl text-xs font-semibold border shrink-0 transition-all">
                    🎨 Creative &amp; Product UX
                </button>
                <button @click="selectedCategory = 'marketing'; showToast('Filtering parameters: Growth & SEO...', 'info')"
                        :class="selectedCategory === 'marketing' ? 'bg-brand-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-200'"
                        class="px-4.5 py-2 rounded-xl text-xs font-semibold border shrink-0 transition-all">
                    📈 Marketing &amp; Operations
                </button>
                <button @click="selectedCategory = 'writer'; showToast('Filtering parameters: Technical Copywriters...', 'info')"
                        :class="selectedCategory === 'writer' ? 'bg-brand-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-200'"
                        class="px-4.5 py-2 rounded-xl text-xs font-semibold border shrink-0 transition-all">
                    ✍️ Technical Writing
                </button>
            </div>
        </div>
    </section>

    <!-- Main Search & Results Workspace Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar: Search & Filter Console (Desktop view: static, Mobile view: responsive overlay) -->
            <aside class="hidden lg:block space-y-6">
                <div class="glassmorphism rounded-2xl p-6 border border-slate-800/80 space-y-6 sticky top-24">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                        <h2 class="text-sm font-bold text-white tracking-wide uppercase">Workspace Filters</h2>
                        <button @click="searchQuery = ''; selectedCategory = 'all'; selectedExperience = 'all'; maxRate = 150; onlyAvailable = false; showToast('Reset filter configuration parameters', 'info')" 
                                class="text-[10px] font-bold text-slate-400 hover:text-brand-400 transition-all">
                            Reset All
                        </button>
                    </div>

                    <!-- Filter 1: Keywords -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-300">Detailed Search</label>
                        <div class="relative">
                            <input x-model="searchQuery" type="text" placeholder="Search skills, names, languages..."
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter 2: Max Hourly Rate -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-300">Max Budget Scale</span>
                            <span class="font-mono text-brand-400 font-bold" x-text="'$' + maxRate + '/hr'">$150/hr</span>
                        </div>
                        <input x-model="maxRate" type="range" min="40" max="200" step="5"
                               class="w-full accent-brand-500 h-1 bg-slate-950 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>$40/hr</span>
                            <span>$200/hr</span>
                        </div>
                    </div>

                    <!-- Filter 3: Experience Level -->
                    <div class="space-y-2.5">
                        <label class="block text-xs font-bold text-slate-300">Experience Tier</label>
                        <select x-model="selectedExperience" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-300 focus:outline-none focus:border-brand-500">
                            <option value="all">Any Level</option>
                            <option value="mid">Mid-Weight (3-5 years)</option>
                            <option value="senior">Senior Lead (5-8 years)</option>
                            <option value="expert">Principal Architect (8+ years)</option>
                        </select>
                    </div>

                    <!-- Filter 4: Only Available Now -->
                    <div class="flex items-center justify-between p-3.5 bg-slate-950 rounded-xl border border-slate-800/80">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-bold text-slate-300">Immediate Integration</span>
                            <span class="text-[9px] text-slate-500">Show only available profiles</span>
                        </div>
                        <button @click="onlyAvailable = !onlyAvailable"
                                :class="onlyAvailable ? 'bg-emerald-500' : 'bg-slate-800'"
                                class="w-11 h-6 rounded-full relative transition-colors duration-200">
                            <span :class="onlyAvailable ? 'translate-x-6' : 'translate-x-1'"
                                  class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full transition-transform duration-200"></span>
                        </button>
                    </div>

                    <!-- Operational Guarantee Notice -->
                    <div class="p-4 rounded-xl bg-brand-950/20 border border-brand-500/15">
                        <div class="flex items-start gap-2">
                            <span class="text-brand-400 mt-0.5 shrink-0">🛡️</span>
                            <div>
                                <p class="text-[10px] font-bold text-brand-300">Pre-Vetted Standard</p>
                                <p class="text-[9px] text-slate-400 mt-1 leading-relaxed">Each talent undergoes code checks, cultural matching, and identity screening before platform approval.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- Search Results Display Area -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Search Result Summary Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-900">
                    <div class="text-sm font-semibold text-slate-400">
                        Showing <span class="text-white" x-text="filteredTalents.length"></span> premium talent files
                    </div>

                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <!-- Mobile filter trigger button -->
                        <button @click="mobileFilterOpen = true" class="lg:hidden shrink-0 flex items-center gap-2 px-4 py-2.5 bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filters
                        </button>

                        <div class="flex items-center gap-2 ml-auto">
                            <span class="text-xs text-slate-500">Sort By</span>
                            <select x-model="sortBy" class="bg-slate-900 border border-slate-800 rounded-xl p-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-brand-500">
                                <option value="rating">Highest Rated</option>
                                <option value="rate-asc">Rate: Low to High</option>
                                <option value="rate-desc">Rate: High to Low</option>
                                <option value="reviews">Most Reviewed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Talent Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template x-for="talent in filteredTalents" :key="talent.id">
                        
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-brand-500/50 hover:shadow-xl hover:shadow-brand-950/5 transition-all flex flex-col justify-between group">
                            <div>
                                <!-- Top: Category badge + Availability indicator -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-bold tracking-wider uppercase bg-brand-500/10 text-brand-300 px-2.5 py-1 rounded-full"
                                          x-text="talent.category === 'dev' ? 'Systems Engineer' : talent.category === 'design' ? 'Product Design' : talent.category === 'marketing' ? 'Growth Specialist' : 'Content Writer'"></span>
                                    
                                    <span class="flex items-center gap-1">
                                        <span :class="talent.available ? 'bg-emerald-500' : 'bg-slate-600'" class="w-1.5 h-1.5 rounded-full"></span>
                                        <span x-text="talent.available ? 'Available Now' : 'Booked'" class="text-[10px] font-semibold text-slate-400"></span>
                                    </span>
                                </div>

                                <!-- Middle: Profile picture & header core info -->
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="relative shrink-0">
                                        <img :src="talent.image" :alt="talent.name" class="w-14 h-14 rounded-full object-cover border-2 border-slate-800">
                                        <span x-show="talent.rating >= 4.9" class="absolute -bottom-1 -right-1 bg-amber-500 text-dark-950 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold border-2 border-slate-900">⭐</span>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-white group-hover:text-brand-300 transition-colors flex items-center gap-1.5">
                                            <span x-text="talent.name"></span>
                                            <!-- Blue premium checked mark -->
                                            <span class="text-brand-400" title="Vetted Professional">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            </span>
                                        </h3>
                                        <p class="text-xs text-slate-400 mt-0.5" x-text="talent.role"></p>
                                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500">
                                            <span class="font-bold text-amber-400" x-text="'⭐ ' + talent.rating"></span>
                                            <span x-text="'(' + talent.reviews + ' reviews)'"></span>
                                            <span>•</span>
                                            <span x-text="talent.location"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brief Description Bio -->
                                <p class="text-xs text-slate-400 leading-relaxed mb-6 block h-12 overflow-hidden text-ellipsis" x-text="talent.bio"></p>

                                <!-- Skill Chip tags -->
                                <div class="flex flex-wrap gap-1.5 mb-6">
                                    <template x-for="skill in talent.skills.slice(0, 4)">
                                        <span class="text-[10px] font-medium bg-slate-850 text-slate-300 px-2.5 py-1 rounded-lg" x-text="skill"></span>
                                    </template>
                                    <span x-show="talent.skills.length > 4" class="text-[10px] font-medium bg-slate-850 text-slate-500 px-2 py-1 rounded-lg" x-text="'+' + (talent.skills.length - 4) + ' more'"></span>
                                </div>
                            </div>

                            <!-- Bottom: Rate & Interactive CTAs -->
                            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Hourly Rate</p>
                                    <p class="text-base font-extrabold text-white" x-text="'$' + talent.rate + '/hr'"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- View Profile detail button -->
                                    <button @click="openDetailModal(talent)" 
                                            class="p-2.5 rounded-xl border border-slate-800 bg-slate-900/40 text-slate-300 hover:text-white hover:bg-slate-800 transition-all text-xs font-bold"
                                            title="View Portfolio & Metrics">
                                        Inspect File
                                    </button>
                                    <!-- Direct hire modal trigger -->
                                    <button @click="selectedTalentName = talent.name; showHireModal = true" 
                                            class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition-colors">
                                        Hire
                                    </button>
                                </div>
                            </div>
                        </div>

                    </template>
                </div>

                <!-- Empty state fallback component -->
                <div x-show="filteredTalents.length === 0" 
                     class="text-center p-12 bg-slate-900/30 border border-slate-800/80 rounded-2xl space-y-4"
                     style="display: none;">
                    <div class="text-4xl">🔍</div>
                    <h3 class="text-base font-bold text-white">No Profiles Found</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">We couldn't locate pre-vetted matching configurations. Try resetting your price filters or searching other domain expertise.</p>
                    <button @click="searchQuery = ''; selectedCategory = 'all'; selectedExperience = 'all'; maxRate = 150; onlyAvailable = false" 
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-xs font-semibold rounded-xl text-white transition-all">
                        Reset Advanced Search Parameters
                    </button>
                </div>

            </div>

        </div>
    </main>

    <!-- Mobile Filter Slide-Over Panel -->
    <div x-show="mobileFilterOpen" 
         class="fixed inset-0 z-50 flex justify-end bg-dark-950/80 backdrop-blur-md" 
         style="display: none;"
         @keydown.escape.window="mobileFilterOpen = false">
        
        <div @click.away="mobileFilterOpen = false" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-full max-w-sm bg-slate-900 border-l border-slate-800 p-6 flex flex-col justify-between">
            
            <div class="space-y-6 overflow-y-auto pr-2">
                <div class="flex items-center justify-between pb-4 border-b border-slate-850">
                    <h3 class="text-sm font-bold text-white tracking-wider uppercase">Mobile Filters</h3>
                    <button @click="mobileFilterOpen = false" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Detailed Mobile Keyword Search -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300">Detailed Search</label>
                    <input x-model="searchQuery" type="text" placeholder="Search skills, names, roles..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white">
                </div>

                <!-- Mobile Hourly Slider -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-300">Max Budget Rate</span>
                        <span class="font-mono text-brand-400 font-bold" x-text="'$' + maxRate + '/hr'"></span>
                    </div>
                    <input x-model="maxRate" type="range" min="40" max="200" step="5" class="w-full accent-brand-500">
                </div>

                <!-- Mobile Experience dropdown -->
                <div class="space-y-2.5">
                    <label class="block text-xs font-bold text-slate-300">Experience Tier</label>
                    <select x-model="selectedExperience" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-300">
                        <option value="all">Any Level</option>
                        <option value="mid">Mid-Weight (3-5 years)</option>
                        <option value="senior">Senior Lead (5-8 years)</option>
                        <option value="expert">Principal Architect (8+ years)</option>
                    </select>
                </div>

                <!-- Mobile switch Toggle available -->
                <div class="flex items-center justify-between p-3.5 bg-slate-950 rounded-xl border border-slate-850">
                    <span class="text-xs font-bold text-slate-300">Immediate Integration</span>
                    <button @click="onlyAvailable = !onlyAvailable"
                            :class="onlyAvailable ? 'bg-emerald-500' : 'bg-slate-800'"
                            class="w-11 h-6 rounded-full relative transition-colors duration-200">
                        <span :class="onlyAvailable ? 'translate-x-6' : 'translate-x-1'"
                              class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full transition-transform"></span>
                    </button>
                </div>
            </div>

            <button @click="mobileFilterOpen = false; showToast('Filters applied successfully!', 'success')" 
                    class="w-full py-4 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl transition-all">
                Apply Search Configurations
            </button>
        </div>
    </div>

    <!-- Footer Section -->
    <?php include_once 'footer.html' ?>

    <!-- Detailed Talent Inspector Modal -->
    <div x-show="showDetailModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/80 backdrop-blur-md"
         style="display: none;"
         @keydown.escape.window="showDetailModal = false">
        
        <div @click.away="showDetailModal = false" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl relative">
            
            <button @click="showDetailModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Inspector header -->
            <div class="flex flex-col sm:flex-row gap-5 pb-6 border-b border-slate-800/85">
                <img :src="activeTalent.image" :alt="activeTalent.name" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-brand-500/50">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-white" x-text="activeTalent.name"></h2>
                        <span class="text-brand-400" title="Vetted Professional Verified">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </span>
                    </div>
                    <p class="text-sm text-brand-400 font-semibold" x-text="activeTalent.role"></p>
                    <p class="text-xs text-slate-400 mt-1" x-text="'Based in ' + activeTalent.location + ' • Approved Member Pool'"></p>
                    
                    <div class="flex gap-4 mt-3">
                        <div class="text-[11px] text-slate-400">Rating: <span class="font-bold text-amber-400" x-text="'⭐ ' + activeTalent.rating"></span></div>
                        <div class="text-[11px] text-slate-400">Rate: <span class="font-bold text-white" x-text="'$' + activeTalent.rate + '/hr'"></span></div>
                    </div>
                </div>
            </div>

            <!-- Inspector body tabs/content -->
            <div class="py-6 space-y-6">
                
                <!-- Core statistics indicators -->
                <div class="grid grid-cols-3 gap-4 bg-slate-950 p-4 rounded-xl border border-slate-800/80 text-center">
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Project Completion</p>
                        <p class="text-base font-black text-white mt-0.5" x-text="activeTalent.metrics?.completionRate"></p>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Hours Tracked</p>
                        <p class="text-base font-black text-white mt-0.5" x-text="activeTalent.metrics?.hoursLogged"></p>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Active Channels</p>
                        <p class="text-base font-black text-white mt-0.5" x-text="activeTalent.metrics?.activeContracts"></p>
                    </div>
                </div>

                <!-- Deep Bio -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Professional Blueprint</h4>
                    <p class="text-xs text-slate-300 leading-relaxed" x-text="activeTalent.bio"></p>
                </div>

                <!-- Full Skills Set -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Full Tech Capability Stack</h4>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="skill in activeTalent.skills">
                            <span class="text-[10px] font-semibold bg-slate-800 text-brand-300 border border-brand-500/10 px-3 py-1 rounded-lg" x-text="skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Highlight Projects Case Studies -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Verified Marketplace Milestones</h4>
                    <div class="space-y-2.5">
                        <template x-for="project in activeTalent.projects">
                            <div class="bg-slate-950 border border-slate-850 p-3 rounded-xl flex items-start gap-3">
                                <span class="text-brand-400 mt-0.5 shrink-0">🚀</span>
                                <div>
                                    <p class="text-xs font-bold text-white" x-text="project.name"></p>
                                    <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed" x-text="project.description"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Inspector footer CTAs -->
            <div class="pt-6 border-t border-slate-800/85 flex gap-4">
                <button @click="showDetailModal = false" class="w-1/2 py-3 border border-slate-800 hover:border-slate-700 bg-slate-900/40 text-slate-300 hover:text-white text-xs font-bold rounded-xl transition-all">
                    Dismiss Inspector
                </button>
                <button @click="showDetailModal = false; selectedTalentName = activeTalent.name; showHireModal = true" 
                        class="w-1/2 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                    Draft Workspace Offer
                </button>
            </div>

        </div>
    </div>

    <!-- Modal: Hire Talent Workspace Offer -->
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

</body>
</html>