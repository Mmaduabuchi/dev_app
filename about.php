<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Devhire Premium Marketplace</title>
    
    <!-- Google Fonts: Inter for crisp, modern tech typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for lightweight interactive components -->
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

    /* Interactive Team Filtering State */
    selectedDepartment: 'all',

    /* Selected Team Member Modal */
    showMemberModal: false,
    activeMember: {},

    /* Interactive Timeline State */
    activeYear: '2026',

    /* Interactive Team Database */
    team: [
        {
            name: 'Sarah Drasner',
            role: 'Co-Founder & Chief Executive Officer',
            department: 'exec',
            image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop',
            bio: 'Formerly VP of Product at Netlify. Developer advocate, author, and technical architect with 14+ years of industry experience scaling digital systems.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-brand-500/50'
        },
        {
            name: 'Vikram Patel',
            role: 'Co-Founder & Chief Technology Officer',
            department: 'exec',
            image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=256&auto=format&fit=crop',
            bio: 'Ex-Stripe principal architect. Designed high-availability ledger databases and global payout structures handling billions of API requests.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-brand-500/50'
        },
        {
            name: 'Aris Thorne',
            role: 'VP of Engineering',
            department: 'eng',
            image: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=256&auto=format&fit=crop',
            bio: 'Core system architect. Former Tech Lead at Vercel with a deep passion for Next.js, compiler optimization, and Rust tooling chains.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-indigo-500/30'
        },
        {
            name: 'Mei-Ling Zhou',
            role: 'Director of Product Design',
            department: 'eng',
            image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop',
            bio: 'Principal UI/UX Architect with ex-Figma design backgrounds. Specialized in design systems, collaborative workspaces, and high-converting checkouts.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-indigo-500/30'
        },
        {
            name: 'Damien Mercer',
            role: 'Head of Global Recruitment',
            department: 'growth',
            image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&auto=format&fit=crop',
            bio: 'Led executive scaling initiatives at Airbnb. Dedicated to sourcing, vetting, and matching the top 3% of global digital talent.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-violet-500/30'
        },
        {
            name: 'Elspeth Vance',
            role: 'Chief Operations Council',
            department: 'growth',
            image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=256&auto=format&fit=crop',
            bio: 'Legal ops and structural scaling specialist. Ensuring regulatory compliance across 85+ global jurisdictions for freelance contracts.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-violet-500/30'
        },
        {
            name: 'Michael Rasmussen',
            role: 'Investor & Strategic Advisor',
            department: 'advisors',
            image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&auto=format&fit=crop',
            bio: 'General Partner at Apex Ventures. Active backing partner for over 45+ B2B SaaS hyper-growth platforms.',
            twitter: '#',
            linkedin: '#',
            accent: 'border-slate-800/80'
        }
    ],

    openMemberModal(member) {
        this.activeMember = member;
        this.showMemberModal = true;
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

    <!-- Hero / Vision Section -->
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 bg-dark-950 overflow-hidden">
        <!-- Background Radial glow -->
        <div class="absolute top-[-40%] left-1/2 -translate-x-1/2 w-[80%] h-[400px] bg-brand-500/15 rounded-full filter blur-[100px] pointer-events-none animate-pulse-slow"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Our Shared Blueprint</span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white mt-3 tracking-tight leading-tight">
                Pioneering the future <br class="hidden sm:inline">
                of <span class="bg-gradient-to-r from-slate-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent">distributed digital work</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-400 mt-6 max-w-3xl mx-auto leading-relaxed">
                At Devhire, we believe expertise has no zip code. Our mission is to accelerate global product cycles by bridging the gap between vetted digital pioneers and scaling enterprise teams safely, transparently, and instantly.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <button @click="showToast('Opening contact request workspace...', 'info')"
                        class="px-8 py-3.5 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 rounded-xl font-bold text-white shadow-lg shadow-brand-500/25 transition-all text-sm">
                    Inquire Partnership
                </button>
                <a href="explore.html" class="px-8 py-3.5 bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-xl font-bold text-slate-300 hover:text-white text-sm transition-all flex items-center justify-center gap-1.5">
                    Browse Talent Files
                </a>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="py-20 bg-dark-900 border-y border-slate-900/60 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Operating Principles</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Foundations of Devhire</h2>
                <p class="text-slate-400 mt-4 text-sm sm:text-base">Guiding how we secure platforms, coordinate contracts, and empower independent digital structures.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Card 1: Vetting integrity -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-8 hover:border-brand-500/40 hover:shadow-xl hover:shadow-brand-950/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-6 group-hover:bg-brand-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Absolute Vetting Integrity</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">We skip buzzwords. Every engineer, designer, and writer undergoes real-world assessments, peer code analysis, and live communication validation before boarding.</p>
                </div>

                <!-- Card 2: Velocity scale -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-8 hover:border-brand-500/40 hover:shadow-xl hover:shadow-brand-950/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center mb-6 group-hover:bg-violet-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Frictionless Integration Velocity</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Contracts are drafted, validated, and loaded inside 48 hours. Zero complex onboarding threads, zero compliance hold-backs.</p>
                </div>

                <!-- Card 3: Democratic globalism -->
                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-8 hover:border-brand-500/40 hover:shadow-xl hover:shadow-brand-950/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Borderless Financial Equity</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">We protect payroll pathways across 85 countries. Ensuring digital experts receive high rates directly while simplifying enterprise taxes.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Meet the Team Directory (Filterable Grid) -->
    <section class="py-24 bg-dark-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Architects of the Platform</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Meet Our Team</h2>
                    <p class="text-slate-400 mt-2 text-sm sm:text-base">An fully remote company founded by builders, product specialists, and operators.</p>
                </div>

                <!-- Interactive Department Filter Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button @click="selectedDepartment = 'all'; showToast('Showing all team folders...', 'info')"
                            :class="selectedDepartment === 'all' ? 'bg-brand-600 text-white border-brand-500' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                        All Members
                    </button>
                    <button @click="selectedDepartment = 'exec'; showToast('Showing leadership folder...', 'info')"
                            :class="selectedDepartment === 'exec' ? 'bg-brand-600 text-white border-brand-500' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                        Leadership
                    </button>
                    <button @click="selectedDepartment = 'eng'; showToast('Showing engineering & design folders...', 'info')"
                            :class="selectedDepartment === 'eng' ? 'bg-brand-600 text-white border-brand-500' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                        Engineering / Design
                    </button>
                    <button @click="selectedDepartment = 'growth'; showToast('Showing operations folder...', 'info')"
                            :class="selectedDepartment === 'growth' ? 'bg-brand-600 text-white border-brand-500' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
                        Operations / Talent
                    </button>
                </div>
            </div>

            <!-- Team Members Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <template x-for="member in team" :key="member.name">
                    <div x-show="selectedDepartment === 'all' || member.department === selectedDepartment"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="scale-95 opacity-0"
                         x-transition:enter-end="scale-100 opacity-100"
                         :class="member.accent"
                         class="bg-slate-900/40 border rounded-2xl p-6 flex flex-col justify-between group hover:border-brand-500/50 transition-all">
                        <div>
                            <div class="relative overflow-hidden rounded-xl mb-4">
                                <img :src="member.image" :alt="member.name" class="w-full h-48 object-cover rounded-xl transform group-hover:scale-105 transition-all duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                                    <span class="text-[10px] font-bold tracking-wider uppercase bg-brand-600 text-white px-2 py-1 rounded">View Profile Card</span>
                                </div>
                            </div>

                            <h3 class="text-base font-bold text-white group-hover:text-brand-300 transition-colors" x-text="member.name"></h3>
                            <p class="text-xs text-brand-400 font-semibold mt-0.5" x-text="member.role"></p>
                            <p class="text-[11px] text-slate-400 leading-relaxed mt-3 block h-16 overflow-hidden text-ellipsis" x-text="member.bio"></p>
                        </div>

                        <div class="pt-4 border-t border-slate-850 flex items-center justify-between mt-4">
                            <!-- Social icons -->
                            <div class="flex gap-2.5 text-slate-400">
                                <a :href="member.twitter" class="hover:text-white transition-colors">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 4.56a9.83 9.83 0 01-2.83.77 4.93 4.93 0 002.17-2.72 9.86 9.86 0 01-3.13 1.2 4.92 4.92 0 00-8.38 4.48A13.98 13.98 0 011.67 3.15 4.93 4.93 0 003.2 9.72a4.89 4.89 0 01-2.23-.61v.06a4.92 4.92 0 003.95 4.83 4.9 4.9 0 01-2.22.08 4.93 4.93 0 004.6 3.42A9.9 9.9 0 010 19.54a13.94 13.94 0 007.55 2.21c9.06 0 14-7.5 14-14 0-.21 0-.42-.01-.63A10.02 10.02 0 0024 4.56z"/></svg>
                                </a>
                                <a :href="member.linkedin" class="hover:text-white transition-colors">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </a>
                            </div>

                            <button @click="openMemberModal(member)"
                                    class="text-[10px] font-bold text-slate-300 hover:text-brand-400 group-hover:underline flex items-center gap-1">
                                Profile Card
                                <svg class="w-3 h-3 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>

            </div>

        </div>
    </section>

    <!-- Interactive Timeline Milestones Section -->
    <section class="py-24 bg-dark-900 border-t border-slate-900/60 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Our Chronicles</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Journey &amp; Growth Map</h2>
                <p class="text-slate-400 mt-3 text-xs sm:text-sm">Click each year indicator below to examine our primary platform accomplishments.</p>
            </div>

            <!-- Years slider controls -->
            <div class="flex items-center justify-center gap-2 sm:gap-6 pb-8 border-b border-slate-800/80 max-w-xl mx-auto">
                <button @click="activeYear = '2022'; showToast('Timeline: Seed Phase', 'info')"
                        :class="activeYear === '2022' ? 'bg-brand-600 text-white scale-110' : 'bg-slate-950 text-slate-500 hover:text-slate-300'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0">
                    2022
                </button>
                <span class="h-[2px] w-6 bg-slate-800"></span>
                <button @click="activeYear = '2023'; showToast('Timeline: Alpha Launch', 'info')"
                        :class="activeYear === '2023' ? 'bg-brand-600 text-white scale-110' : 'bg-slate-950 text-slate-500 hover:text-slate-300'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0">
                    2023
                </button>
                <span class="h-[2px] w-6 bg-slate-800"></span>
                <button @click="activeYear = '2024'; showToast('Timeline: Venture Series', 'info')"
                        :class="activeYear === '2024' ? 'bg-brand-600 text-white scale-110' : 'bg-slate-950 text-slate-500 hover:text-slate-300'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0">
                    2024
                </button>
                <span class="h-[2px] w-6 bg-slate-800"></span>
                <button @click="activeYear = '2025'; showToast('Timeline: Escrow Platform Update', 'info')"
                        :class="activeYear === '2025' ? 'bg-brand-600 text-white scale-110' : 'bg-slate-950 text-slate-500 hover:text-slate-300'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0">
                    2025
                </button>
                <span class="h-[2px] w-6 bg-slate-800"></span>
                <button @click="activeYear = '2026'; showToast('Timeline: Global Domination', 'info')"
                        :class="activeYear === '2026' ? 'bg-brand-600 text-white scale-110' : 'bg-slate-950 text-slate-500 hover:text-slate-300'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0">
                    2026
                </button>
            </div>

            <!-- Timeline Content Cards -->
            <div class="mt-12 max-w-3xl mx-auto relative">
                
                <!-- 2022 -->
                <div x-show="activeYear === '2022'" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="glassmorphism p-8 rounded-3xl border border-slate-800/80 relative overflow-hidden flex flex-col sm:flex-row gap-6 items-center">
                    <div class="text-6xl font-black text-slate-800/40 select-none shrink-0 sm:text-7xl">'22</div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest uppercase bg-brand-500/15 text-brand-300 px-2 py-0.5 rounded">Conceptual Blueprint</span>
                        <h3 class="text-xl font-bold text-white mt-1.5">Devhire Genesis &amp; Prototyping</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">The original prototype for borderless global payouts was launched. Sourced seed fundings from preeminent developers who realized the friction points of modern contracting networks.</p>
                        <div class="flex gap-4 mt-4 text-[10px] text-slate-500 font-mono">
                            <span>🚀 4 Team Core</span>
                            <span>⚡ Proof-Of-Concept Settle</span>
                        </div>
                    </div>
                </div>

                <!-- 2023 -->
                <div x-show="activeYear === '2023'" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="glassmorphism p-8 rounded-3xl border border-slate-800/80 relative overflow-hidden flex flex-col sm:flex-row gap-6 items-center"
                     style="display: none;">
                    <div class="text-6xl font-black text-slate-800/40 select-none shrink-0 sm:text-7xl">'23</div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest uppercase bg-brand-500/15 text-brand-300 px-2 py-0.5 rounded">Beta Settle</span>
                        <h3 class="text-xl font-bold text-white mt-1.5">Platform Launch &amp; Beta Auditing</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Launched our initial private vetting engines. Vetted 1,200 systems engineers and successfully onboarded early startup companies to prove matching logic.</p>
                        <div class="flex gap-4 mt-4 text-[10px] text-slate-500 font-mono">
                            <span>📱 1,200 Vetted</span>
                            <span>💰 $2M Managed volume</span>
                        </div>
                    </div>
                </div>

                <!-- 2024 -->
                <div x-show="activeYear === '2024'" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="glassmorphism p-8 rounded-3xl border border-slate-800/80 relative overflow-hidden flex flex-col sm:flex-row gap-6 items-center"
                     style="display: none;">
                    <div class="text-6xl font-black text-slate-800/40 select-none shrink-0 sm:text-7xl">'24</div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest uppercase bg-brand-500/15 text-brand-300 px-2 py-0.5 rounded">Series Funding</span>
                        <h3 class="text-xl font-bold text-white mt-1.5">Venturing scale-up round</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Secured $12.5M Series A backing led by top fintech institutional partners. Expanded matching velocity structures, doubling our engineering workforce.</p>
                        <div class="flex gap-4 mt-4 text-[10px] text-slate-500 font-mono">
                            <span>🏢 28 Team Core</span>
                            <span>💼 $12.5M Raised</span>
                        </div>
                    </div>
                </div>

                <!-- 2025 -->
                <div x-show="activeYear === '2025'" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="glassmorphism p-8 rounded-3xl border border-slate-800/80 relative overflow-hidden flex flex-col sm:flex-row gap-6 items-center"
                     style="display: none;">
                    <div class="text-6xl font-black text-slate-800/40 select-none shrink-0 sm:text-7xl">'25</div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest uppercase bg-brand-500/15 text-brand-300 px-2 py-0.5 rounded">Platform Innovation</span>
                        <h3 class="text-xl font-bold text-white mt-1.5">Automated Escrow &amp; Matching AI</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Engineered automated smart matching protocols. Integrated timezone optimization structures and dynamic escrow accounts ensuring borderless payment defense.</p>
                        <div class="flex gap-4 mt-4 text-[10px] text-slate-500 font-mono">
                            <span>🛠️ Escrow Settle</span>
                            <span>🎯 98.4% Match Accuracy</span>
                        </div>
                    </div>
                </div>

                <!-- 2026 -->
                <div x-show="activeYear === '2026'" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="glassmorphism p-8 rounded-3xl border border-slate-800/80 relative overflow-hidden flex flex-col sm:flex-row gap-6 items-center">
                    <div class="text-6xl font-black text-slate-800/40 select-none shrink-0 sm:text-7xl">'26</div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest uppercase bg-brand-500/15 text-brand-300 px-2 py-0.5 rounded">Present State</span>
                        <h3 class="text-xl font-bold text-white mt-1.5">Scaling global tech frameworks</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Now serving as the premium tech marketplace for over 2,400+ SaaS platforms. Empowering 15,000+ active specialists to collaborate freely, regardless of borderlines.</p>
                        <div class="flex gap-4 mt-4 text-[10px] text-slate-500 font-mono">
                            <span>🌍 Global Network</span>
                            <span>💼 15,000+ Verified Profiles</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Distributed Footprint map placeholder section -->
    <section class="py-24 bg-dark-950 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <div class="max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-400">Distributed Globalism</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Distributed globally, focused hyper-locally</h2>
                <p class="text-slate-400 mt-3 text-xs sm:text-sm">Our primary corporate nodes are coordinates across 85 countries, supporting synchronized workspaces for premium digital creation.</p>
            </div>

            <!-- Graphic map mockup -->
            <div class="glassmorphism border border-slate-800/80 rounded-[32px] p-6 sm:p-12 relative overflow-hidden min-h-[300px] flex items-center justify-center">
                
                <!-- SVG Vector pattern illustrating networks -->
                <div class="absolute inset-0 opacity-15 select-none pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 800 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="200" cy="150" r="4" fill="#6366f1" />
                        <circle cx="350" cy="220" r="4" fill="#6366f1" />
                        <circle cx="500" cy="120" r="4" fill="#6366f1" />
                        <circle cx="600" cy="280" r="4" fill="#6366f1" />
                        
                        <path d="M200 150 Q275 185 350 220" stroke="#6366f1" stroke-width="1" stroke-dasharray="4 4" />
                        <path d="M350 220 Q425 170 500 120" stroke="#6366f1" stroke-width="1" stroke-dasharray="4 4" />
                        <path d="M500 120 Q550 200 600 280" stroke="#6366f1" stroke-width="1" stroke-dasharray="4 4" />
                        <path d="M200 150 Q350 135 500 120" stroke="#4f46e5" stroke-width="1.5" />
                    </svg>
                </div>

                <div class="space-y-6 relative z-10">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div>
                            <p class="text-2xl sm:text-3xl font-black text-white">85+</p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase mt-1">Countries Supported</p>
                        </div>
                        <div>
                            <p class="text-2xl sm:text-3xl font-black text-white">24/7</p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase mt-1">Support Settle</p>
                        </div>
                        <div>
                            <p class="text-2xl sm:text-3xl font-black text-white">&lt; 48h</p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase mt-1">Onboarding Velocity</p>
                        </div>
                        <div>
                            <p class="text-2xl sm:text-3xl font-black text-brand-400">Zero</p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase mt-1">Cross-Border Tariffs</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- CTA Onboarding Platform Section -->
    <section class="py-24 bg-dark-900 border-t border-slate-900/60 relative overflow-hidden">
        <div class="absolute inset-0 bg-brand-500/5 rounded-full filter blur-[100px] pointer-events-none transform scale-75"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="glassmorphism rounded-[32px] p-8 sm:p-16 border border-slate-800 text-center relative overflow-hidden">
                
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-[300px] h-[1px] bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

                <h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Engage the elite talent <br class="hidden sm:inline">
                    force of tomorrow
                </h2>
                <p class="text-slate-400 text-base sm:text-lg max-w-2xl mx-auto mt-6">
                    Join high-growth tech founders scaling remote pipelines through Devhire pre-vetted specialist archives.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-10">
                    <button @click="showToast('Redirecting to startup account creation...', 'info')" 
                            class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 rounded-xl font-bold text-white shadow-xl shadow-brand-500/20 transform hover:-translate-y-0.5 transition-all text-sm">
                        Scale My Tech Team
                    </button>
                    <button @click="showToast('Redirecting to digital talent screening dashboard...', 'info')" 
                            class="w-full sm:w-auto px-8 py-4 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-200 hover:text-white rounded-xl font-bold transition-all text-sm">
                        Apply as Talent
                    </button>
                </div>

                <p class="text-[11px] text-slate-500 mt-6 font-mono">Free directory review • Zero subscription fees to start • Escrow secured</p>

            </div>
        </div>
    </section>

    <!-- Detailed Team Member Modal Inspector -->
    <div x-show="showMemberModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/80 backdrop-blur-md"
         style="display: none;"
         @keydown.escape.window="showMemberModal = false">
        
        <div @click.away="showMemberModal = false" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl relative">
            
            <button @click="showMemberModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Inspector header -->
            <div class="flex items-center gap-5 pb-6 border-b border-slate-800/85">
                <img :src="activeMember.image" :alt="activeMember.name" class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover border-2 border-brand-500/50 shadow-md">
                <div>
                    <h2 class="text-xl font-bold text-white" x-text="activeMember.name"></h2>
                    <p class="text-xs text-brand-400 font-semibold" x-text="activeMember.role"></p>
                    <span class="inline-block mt-2 text-[9px] font-bold tracking-wider bg-slate-950 text-slate-400 border border-slate-800 px-2 py-0.5 rounded uppercase"
                          x-text="activeMember.department === 'exec' ? 'Leadership Council' : activeMember.department === 'eng' ? 'Engineering Node' : activeMember.department === 'growth' ? 'Platform Operations' : 'Platform Advisor'"></span>
                </div>
            </div>

            <!-- Inspector body -->
            <div class="py-6 space-y-4">
                <div class="space-y-1.5">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500">Core Blueprint</h4>
                    <p class="text-xs text-slate-300 leading-relaxed" x-text="activeMember.bio"></p>
                </div>
            </div>

            <!-- Inspector footer controls -->
            <div class="pt-6 border-t border-slate-800/85 flex gap-4">
                <button @click="showMemberModal = false" class="w-full py-3 border border-slate-800 hover:border-slate-700 bg-slate-900/40 text-slate-300 hover:text-white text-xs font-bold rounded-xl transition-all">
                    Dismiss Inspector
                </button>
            </div>

        </div>
    </div>

    <!-- Footer Section -->
    <?php include_once 'footer.html' ?>

</body>
</html>