<script setup>
import { inject, ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '../Components/Toast.vue';
import { alert } from '../Utils/alert';
import { 
    Home, 
    FileText, 
    Users, 
    User, 
    ExternalLink, 
    LogOut, 
    ChevronDown, 
    Landmark 
} from '@lucide/vue';

const progress = inject('pageProgress');
const page = usePage();
const warga = computed(() => page.props.auth?.warga || null);
const showUserMenu = ref(false);

const logout = () => {
    alert.confirm('Keluar Portal?', 'Apakah Anda yakin ingin keluar dari sesi portal warga?', 'Keluar', 'Batal')
        .then((result) => {
            if (result.isConfirmed) {
                router.post('/logout');
            }
        });
};

const currentPath = computed(() => {
    try { return new URL(page.url, window.location.origin).pathname; } catch { return page.url; }
});

const currentTab = computed(() => {
    try {
        const url = new URL(page.url, window.location.origin);
        const tab = url.searchParams.get('tab');
        const path = url.pathname;
        
        if (path.startsWith('/warga/profil') || tab === 'biodata') return 'biodata';
        if (path.startsWith('/warga/keluarga') || tab === 'keluarga') return 'keluarga';
        if (path.startsWith('/warga/surat') || path.startsWith('/warga/pengajuan') || tab === 'pengajuan') return 'pengajuan';
        return 'beranda';
    } catch {
        return 'beranda';
    }
});

const navItems = [
    { label: 'Beranda', href: '/warga/dashboard?tab=beranda', tabId: 'beranda', icon: Home },
    { label: 'Layanan', href: '/warga/dashboard?tab=pengajuan', tabId: 'pengajuan', icon: FileText },
    { label: 'Keluarga', href: '/warga/keluarga', tabId: 'keluarga', icon: Users },
    { label: 'Profil', href: '/warga/profil', tabId: 'biodata', icon: User },
];

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
    <div class="google-editorial min-h-screen pb-20 md:pb-6">
        <div v-if="progress" class="fixed top-0 left-0 right-0 z-50 h-1 bg-blue-100 overflow-hidden">
            <div class="h-full bg-blue-600 w-1/3 rounded-full animate-progress"></div>
        </div>

        <header class="sticky top-0 z-40 border-b border-slate-200/50 bg-white/90 backdrop-blur-md hidden md:block">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-8">
                    <Link href="/warga/dashboard" class="flex items-center gap-2.5">
                        <div class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-[0_4px_12px_rgba(26,115,232,0.18)]">
                            <Landmark class="size-4.5 text-white" />
                        </div>
                        <div>
                            <span class="text-base font-bold text-neutral tracking-tight">Portal Warga</span>
                            <p class="text-[8px] font-bold text-primary uppercase tracking-wider mt-0.5">AvaraDesa Digital</p>
                        </div>
                    </Link>
                    
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="item in navItems" 
                            :key="item.href" 
                            :href="item.href"
                            class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-200"
                            :class="currentTab === item.tabId ? 'bg-primary-soft text-primary font-semibold' : 'text-secondary hover:bg-slate-100/70 hover:text-neutral'"
                        >
                            {{ item.label }}
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Link href="/" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 hover:border-slate-350 bg-white px-4.5 py-2 text-xs font-semibold text-secondary hover:text-neutral transition duration-150">
                        <ExternalLink class="size-3.5 text-secondary" />
                        Portal Publik
                    </Link>

                    <div class="relative">
                        <button
                            @click="showUserMenu = !showUserMenu"
                            class="flex items-center gap-2.5 rounded-xl border border-slate-200/50 bg-slate-50/50 hover:bg-slate-100/50 px-3 py-1.5 transition-all duration-200 cursor-pointer"
                        >
                            <div class="flex size-8 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold text-primary ring-2 ring-blue-50/50 overflow-hidden shrink-0">
                                <img v-if="warga?.foto_profil" :src="warga.foto_profil" alt="Profil" class="size-full object-cover" />
                                <span v-else>{{ getInitials(warga?.nama_lengkap) }}</span>
                            </div>
                            <span class="max-w-[120px] truncate text-sm font-semibold text-neutral">
                                {{ warga?.nama_lengkap || 'Warga' }}
                            </span>
                            <ChevronDown class="size-4 text-secondary/70 transition-transform duration-200" :class="{ 'rotate-180': showUserMenu }" />
                        </button>

                        <Transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="scale-95 opacity-0"
                            enter-to-class="scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="scale-100 opacity-100"
                            leave-to-class="scale-95 opacity-0"
                        >
                            <div v-if="showUserMenu" class="absolute right-0 top-full mt-2 w-56 rounded-2xl border border-slate-200/60 bg-white p-1.5 shadow-[0_10px_30px_rgba(60,64,67,0.08)]" @click="showUserMenu = false">
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="truncate text-sm font-bold text-neutral leading-tight">{{ warga?.nama_lengkap }}</p>
                                    <p class="truncate text-[10px] text-secondary mt-1 font-semibold uppercase tracking-wider">NIK: {{ warga?.nik }}</p>
                                </div>
                                <div class="py-1">
                                    <Link href="/warga/profil" class="flex items-center gap-2 px-3.5 py-2.5 text-xs font-bold text-secondary hover:text-neutral hover:bg-slate-50 rounded-xl transition">
                                        <User class="size-4 text-secondary/70" />
                                        Biodata Saya
                                    </Link>
                                    <Link href="/warga/keluarga" class="flex items-center gap-2 px-3.5 py-2.5 text-xs font-bold text-secondary hover:text-neutral hover:bg-slate-50 rounded-xl transition">
                                        <Users class="size-4 text-secondary/70" />
                                        Keluarga Saya
                                    </Link>
                                </div>
                                <div class="border-t border-slate-100 pt-1.5">
                                    <button @click="logout" class="flex w-full items-center gap-2 px-3.5 py-2.5 text-xs font-bold text-error hover:bg-red-50/70 rounded-xl transition cursor-pointer">
                                        <LogOut class="size-4 text-error/70" />
                                        Keluar Sesi
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </nav>
        </header>

        <header class="sticky top-0 z-40 border-b border-slate-200/50 bg-white/90 backdrop-blur-md md:hidden px-5 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="flex size-8.5 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-[0_4px_10px_rgba(26,115,232,0.15)]">
                    <Landmark class="size-4 text-white" />
                </div>
                <span class="text-base font-bold text-neutral tracking-tight">Portal Warga</span>
            </div>
            <div class="flex items-center gap-3">
                <Link href="/warga/profil" class="flex size-8 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold text-primary ring-2 ring-blue-50/50 overflow-hidden shrink-0">
                    <img v-if="warga?.foto_profil" :src="warga.foto_profil" alt="Profil" class="size-full object-cover" />
                    <span v-else>{{ getInitials(warga?.nama_lengkap) }}</span>
                </Link>
                <button @click="logout" class="p-1 rounded-xl text-error hover:bg-red-50/60 transition active:scale-95 shrink-0 cursor-pointer">
                    <LogOut class="size-5" />
                </button>
            </div>
        </header>

        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200/70 bg-white/90 pb-safe-bottom shadow-[0_-4px_12px_rgba(60,64,67,0.03)] backdrop-blur-md md:hidden">
            <div class="mx-auto flex h-16 max-w-lg items-center justify-around px-2">
                <Link
                    v-for="item in navItems"
                    :key="item.tabId"
                    :href="item.href"
                    class="flex flex-col items-center justify-center py-1 text-center transition-all duration-200"
                    :class="currentTab === item.tabId ? 'text-primary' : 'text-secondary hover:text-neutral'"
                >
                    <div class="flex items-center justify-center w-14 h-8 rounded-full transition-all duration-200" :class="currentTab === item.tabId ? 'bg-primary-soft text-primary' : 'hover:bg-slate-100/50 text-secondary'">
                        <component :is="item.icon" class="size-5" />
                    </div>
                    <span class="text-[9px] font-semibold tracking-wide mt-1 uppercase">{{ item.label }}</span>
                </Link>
            </div>
        </div>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <slot />
        </main>

        <div v-if="showUserMenu" class="fixed inset-0 z-30" @click="showUserMenu = false" />
    </div>
</template>

<style>


.google-editorial {
    font-family: inherit;
    color: #202124;
    background-color: #F8F9FA;
}

.google-editorial .text-primary { color: #1A73E8; }
.google-editorial .text-secondary { color: #5F6368; }
.google-editorial .text-neutral { color: #202124; }
.google-editorial .text-error { color: #D93025; }

.google-editorial .bg-primary { background-color: #1A73E8; }
.google-editorial .bg-primary-soft { background-color: #E8F0FE; color: #1A73E8; }

.animate-progress {
    animation: progress-indeterminate 1.5s ease-in-out infinite;
}

@keyframes progress-indeterminate {
    0% { transform: translateX(-100%) scaleX(0.5); }
    50% { transform: translateX(100%) scaleX(1); }
    100% { transform: translateX(300%) scaleX(0.5); }
}
</style>
