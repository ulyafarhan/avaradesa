<script setup>
import { inject, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppButton from '../Components/AppButton.vue';
import Toast from '../Components/Toast.vue';
import PageProgressBar from './Partials/PageProgressBar.vue';
import { Menu, X, Phone, Mail, MapPin, Landmark, Clock, ArrowRight, Shield, Send, CheckCircle2, Globe, Heart } from '@lucide/vue';

const page = usePage();
const mobileMenuOpen = ref(false);
const isLoading = inject('pageProgress');

const homeActive = (path) => {
    const url = page.url;
    if (path === '/') return url === '/';
    if (path === 'profil') return url.startsWith('/profil');
    return false;
};

const aspirasiInput = ref('');
const suggestionSent = ref(false);
const sendAspirasi = async () => {
    if (aspirasiInput.value.trim() === '') return;
    try {
        await fetch('/aspirasi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''),
            },
            body: JSON.stringify({ pesan: aspirasiInput.value, _trap: '' }),
        });
    } catch (e) {}
    suggestionSent.value = true;
    setTimeout(() => {
        aspirasiInput.value = '';
        suggestionSent.value = false;
    }, 4000);
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col font-sans w-full overflow-x-hidden text-slate-900">
        <PageProgressBar />
        <Toast />

        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-sm transition-all duration-300">
            <nav class="mx-auto flex max-w-7xl w-full items-center justify-between px-6 py-4 lg:px-8">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-slate-900 text-white transition duration-300 overflow-hidden shrink-0">
                        <img 
                            :src="page.props.settings.logo_desa" 
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" 
                            :class="{ 'invert brightness-0 scale-[1.35] group-hover:scale-[1.45]': page.props.settings.logo_desa === '/logo.svg' }" 
                            alt="Logo"
                        >
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-2xl font-bold tracking-tight text-slate-900 font-heading group-hover:text-amber-600 transition duration-300 leading-none">AvaraDesa</span>
                        <span class="text-[10px] font-bold tracking-widest text-slate-500 uppercase leading-none mt-1">Desa {{ page.props.settings.nama_desa }}</span>
                    </div>
                </Link>

                <div class="hidden items-center gap-1 text-[13px] font-bold text-slate-600 sm:flex uppercase tracking-wider">
                    <Link href="/" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('/') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Beranda</Link>
                    <Link href="/profil" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('profil') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Profil</Link>
                    <Link href="/informasi" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('informasi') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Informasi</Link>
                    <Link href="/statistik" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('statistik') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Statistik</Link>
                    <Link href="/fasilitas" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('fasilitas') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Fasilitas</Link>
                    <Link href="/verifikasi" class="px-4 py-2.5 rounded-md transition-colors duration-200" :class="homeActive('verifikasi') ? 'text-amber-700 bg-amber-50' : 'hover:bg-slate-100 hover:text-slate-900'">Verifikasi</Link>
                </div>

                <div class="hidden items-center gap-4 sm:flex">
                    <Link :href="page.props.auth?.warga ? '/warga/dashboard' : '/login'" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition-all hover:bg-slate-800 hover:shadow-lg hover:shadow-slate-900/20 focus:outline-none focus:ring-4 focus:ring-slate-200">
                        {{ page.props.auth?.warga ? 'Dasbor Warga' : 'Portal Layanan' }}
                    </Link>
                </div>

                <button class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 hover:bg-slate-100 sm:hidden transition" @click="mobileMenuOpen = !mobileMenuOpen">
                    <Menu v-if="!mobileMenuOpen" class="size-6" />
                    <X v-else class="size-6" />
                </button>
            </nav>

            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="-translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="-translate-y-2 opacity-0"
            >
                <div v-if="mobileMenuOpen" class="border-t border-slate-200 bg-white px-6 py-6 shadow-xl sm:hidden">
                    <div class="grid gap-2">
                        <Link href="/" class="rounded-md px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-800 hover:bg-slate-100 transition" @click="mobileMenuOpen = false">Beranda</Link>
                        <Link href="/profil" class="rounded-md px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-800 hover:bg-slate-100 transition" @click="mobileMenuOpen = false">Profil Desa</Link>
                        <Link href="/informasi" class="rounded-md px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-800 hover:bg-slate-100 transition" @click="mobileMenuOpen = false">Informasi</Link>
                        <Link href="/statistik" class="rounded-md px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-800 hover:bg-slate-100 transition" @click="mobileMenuOpen = false">Statistik</Link>
                        <Link href="/verifikasi" class="rounded-md px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-800 hover:bg-slate-100 transition" @click="mobileMenuOpen = false">Verifikasi Surat</Link>
                        
                        <hr class="border-slate-100 my-4">
                        
                        <div class="flex flex-col gap-3 pt-1 text-xs text-slate-600 px-4">
                            <span class="flex items-center gap-2"><Phone class="size-4 text-amber-600" /> {{ page.props.settings.telepon }}</span>
                            <span class="flex items-center gap-2"><Mail class="size-4 text-amber-600" /> {{ page.props.settings.email }}</span>
                        </div>
                        
                        <Link :href="page.props.auth?.warga ? '/warga/dashboard' : '/login'" class="mt-6 flex w-full items-center justify-center rounded-md bg-slate-900 px-6 py-3 text-sm font-bold text-white" @click="mobileMenuOpen = false">
                            {{ page.props.auth?.warga ? 'Dasbor Warga' : 'Portal Layanan' }}
                        </Link>
                    </div>
                </div>
            </Transition>
        </header>

        <main class="flex-grow">
            <Transition name="skeleton-fade" mode="out-in">
                <div v-if="isLoading" class="max-w-7xl mx-auto px-6 py-12 lg:px-8 space-y-8">
                    <div class="skeleton w-2/3 h-10 rounded-lg"></div>
                    <div class="skeleton w-1/2 h-4 rounded"></div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="n in 6" :key="n" class="skeleton skeleton-card rounded-2xl h-56"></div>
                    </div>
                </div>
                <div v-else><slot /></div>
            </Transition>
        </main>

        <!-- Kotak Pengaduan: Redesigned for Mature Professionalism -->
        <section id="aspirasi" class="relative z-30 -mb-24 mx-auto max-w-6xl px-4 sm:px-6 w-full">
            <div class="bg-slate-900 rounded-2xl shadow-2xl p-8 sm:p-12 md:p-16 flex flex-col lg:flex-row items-center justify-between gap-12 text-white border border-slate-800 relative overflow-hidden">
                <!-- Subtle geometric accent -->
                <div class="absolute -right-32 -top-32 w-96 h-96 border-[40px] border-amber-500/10 rounded-full pointer-events-none"></div>
                <div class="absolute -left-32 -bottom-32 w-96 h-96 border-[40px] border-sky-500/10 rounded-full pointer-events-none"></div>
 
                <div class="space-y-4 text-center lg:text-left flex-1 max-w-xl relative z-10">
                    <span class="text-[10px] font-bold text-amber-500 uppercase tracking-[0.2em] inline-block font-sans">
                        Layanan Interaktif
                    </span>
                    <h3 class="text-3xl sm:text-4xl font-bold tracking-tight leading-[1.1] font-heading">Kotak Aspirasi Warga</h3>
                    <p class="text-slate-400 text-base leading-relaxed font-sans">
                        Sampaikan saran, masukan, atau keluhan terkait pelayanan desa. Kami berkomitmen untuk tata kelola yang transparan dan responsif.
                    </p>
                </div>
                
                <div class="w-full lg:w-[400px] shrink-0 relative z-10">
                    <form v-if="!suggestionSent" @submit.prevent="sendAspirasi" class="w-full flex flex-col sm:flex-row gap-3">
                        <input 
                            type="text" 
                            v-model="aspirasiInput"
                            placeholder="Tulis pesan Anda..." 
                            class="grow bg-slate-950/50 text-white placeholder-slate-500 border border-slate-700 rounded-md px-5 py-4 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors font-sans"
                            required
                        />
                        <button 
                            type="submit" 
                            class="rounded-md bg-amber-600 text-white hover:bg-amber-500 font-bold px-8 py-4 text-sm flex items-center justify-center gap-2 shrink-0 transition-colors shadow-lg shadow-amber-900/50"
                        >
                            Kirim <Send class="size-4" />
                        </button>
                    </form>
                    <div v-else class="flex items-center justify-center gap-3 text-emerald-400 bg-emerald-950/40 border border-emerald-900/50 py-4 px-6 rounded-md animate-fade-in">
                        <CheckCircle2 class="size-5 shrink-0" />
                        <span class="text-sm font-medium leading-relaxed font-sans">Aspirasi terkirim. Terima kasih!</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer: Deep Navy Institutional Style -->
        <footer class="bg-slate-950 text-slate-400 pt-32 pb-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-4 pb-16">
                    <div class="space-y-6">
                        <Link href="/" class="flex items-center gap-3 group">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-slate-900 text-white border border-slate-800 overflow-hidden shrink-0">
                                <img 
                                    :src="page.props.settings.logo_desa" 
                                    class="w-full h-full object-cover transition-transform duration-300" 
                                    :class="{ 'invert brightness-0 scale-[1.35]': page.props.settings.logo_desa === '/logo.svg' }" 
                                    alt="Logo"
                                >
                            </div>
                            <div class="flex flex-col justify-center">
                                <span class="text-2xl font-bold tracking-tight text-white leading-none font-heading group-hover:text-amber-500 transition-colors">AvaraDesa</span>
                                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-500 uppercase leading-none mt-1 font-sans">Desa {{ page.props.settings.nama_desa }}</span>
                            </div>
                        </Link>
                        <p class="text-sm leading-relaxed text-slate-400 font-sans">
                            Portal resmi pelayanan publik digital dan keterbukaan informasi Desa {{ page.props.settings.nama_desa }}, Kecamatan {{ page.props.settings.kecamatan }}, {{ page.props.settings.kabupaten }}.
                        </p>
                        <div class="space-y-4 pt-2 text-sm text-slate-300 font-sans">
                            <div class="flex items-start gap-3">
                                <MapPin class="size-4 shrink-0 text-slate-500 mt-0.5" />
                                <span class="leading-relaxed">{{ page.props.settings.alamat }}, Kec. {{ page.props.settings.kecamatan }}, {{ page.props.settings.kabupaten }}, {{ page.props.settings.kode_pos }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Phone class="size-4 shrink-0 text-slate-500" />
                                <span>{{ page.props.settings.telepon }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Mail class="size-4 shrink-0 text-slate-500" />
                                <span>{{ page.props.settings.email }}</span>
                            </div>
                        </div>
                    </div>
 
                    <div class="space-y-6">
                        <h4 class="text-xs font-bold tracking-widest text-white uppercase font-sans border-l-2 border-amber-600 pl-3">Menu Utama</h4>
                        <ul class="space-y-3 text-sm font-sans">
                            <li><Link href="/" class="hover:text-amber-500 transition-colors">Beranda Utama</Link></li>
                            <li><Link href="/profil" class="hover:text-amber-500 transition-colors">Profil & Lembaga</Link></li>
                            <li><Link href="/informasi" class="hover:text-amber-500 transition-colors">Berita & Informasi</Link></li>
                            <li><Link href="/statistik" class="hover:text-amber-500 transition-colors">Statistik Kependudukan</Link></li>
                            <li><Link href="/fasilitas" class="hover:text-amber-500 transition-colors">Fasilitas Desa</Link></li>
                            <li><Link href="/verifikasi" class="hover:text-amber-500 transition-colors">Verifikasi Surat</Link></li>
                        </ul>
                    </div>
 
                    <div class="space-y-6">
                        <h4 class="text-xs font-bold tracking-widest text-white uppercase font-sans border-l-2 border-amber-600 pl-3">Tautan Resmi</h4>
                        <ul class="space-y-3 text-sm font-sans">
                            <li><a href="https://kemendesa.go.id" target="_blank" class="hover:text-amber-500 transition-colors flex items-center justify-between group">Kementerian Desa <ArrowRight class="size-3 text-slate-600 group-hover:text-amber-500 transition-colors" /></a></li>
                            <li><a href="https://pidiejayakab.go.id" target="_blank" class="hover:text-amber-500 transition-colors flex items-center justify-between group">Pemerintah Kab <ArrowRight class="size-3 text-slate-600 group-hover:text-amber-500 transition-colors" /></a></li>
                            <li><a href="https://bps.go.id" target="_blank" class="hover:text-amber-500 transition-colors flex items-center justify-between group">BPS Nasional <ArrowRight class="size-3 text-slate-600 group-hover:text-amber-500 transition-colors" /></a></li>
                            <li><a href="#" class="hover:text-amber-500 transition-colors">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-amber-500 transition-colors">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
 
                    <div class="space-y-6">
                        <h4 class="text-xs font-bold tracking-widest text-white uppercase font-sans border-l-2 border-amber-600 pl-3">Jam Operasional</h4>
                        <div class="text-sm space-y-3 font-sans">
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400">Senin – Kamis</span>
                                <span class="text-white">08.00 – 16.30</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400">Jumat</span>
                                <span class="text-white">08.00 – 16.00</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400">Sabtu – Minggu</span>
                                <span class="text-slate-500">Libur</span>
                            </div>
                        </div>
                        <div class="pt-4">
                            <a href="https://www.google.com/maps/place/5%C2%B016'39.8%22N+96%C2%B006'08.4%22E/@5.2777317,96.1023468,17z" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-800 bg-slate-900 px-5 py-3 text-xs font-bold text-white hover:bg-slate-800 hover:border-slate-700 transition-colors w-full font-sans">
                                <Globe class="size-4 text-slate-400" /> Lihat Peta Lokasi
                            </a>
                        </div>
                    </div>
                </div>
  
                <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-sans">
                    <p>© {{ page.props.settings.tahun_anggaran || '2026' }} Pemerintah Desa {{ page.props.settings.nama_desa }}. Hak Cipta Dilindungi.</p>
                    <p class="flex items-center gap-1">Made with <Heart class="size-3 text-red-500/50" /> &middot; AvaraDesa Digital Platform</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}

/* ponytail: skeleton shimmer */
.skeleton {
    background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 37%, #e2e8f0 63%);
    background-size: 400% 100%;
    animation: skeleton-shimmer 1.4s ease infinite;
}
.skeleton-card { background-color: #f1f5f9; }

@keyframes skeleton-shimmer {
    0% { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}

.skeleton-fade-enter-active,
.skeleton-fade-leave-active {
    transition: opacity 0.2s ease;
}
.skeleton-fade-enter-from,
.skeleton-fade-leave-to {
    opacity: 0;
}
</style>
