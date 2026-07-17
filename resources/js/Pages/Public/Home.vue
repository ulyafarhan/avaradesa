<script setup>
import { onMounted, ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import AppButton from '../../Components/AppButton.vue';
import AppCard from '../../Components/AppCard.vue';
import EmptyState from '../../Components/EmptyState.vue';
import { 
    Users, 
    Home as HomeIcon, 
    FileText, 
    Briefcase, 
    Newspaper, 
    PhoneCall, 
    Fingerprint, 
    Clock, 
    CheckCircle2, 
    ShieldCheck, 
    Send,
    UserCheck,
    User,
    ArrowUpRight,
    MapPin,
    Shield,
    Sparkles,
    BookOpen,
    Activity,
    ArrowRight
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

const stripHtml = (html) => {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '');
};

const props = defineProps({
    demografi: Object,
    layanan: Object,
    berita: Array,
    kategoriSurat: Array,
});

const animatedStats = ref({ penduduk: 0, keluarga: 0, pengajuan: 0, layanan: 0 });
const mapActive = ref(false);

const animateCount = (key, target) => {
    if (!target) return;
    const duration = 1200;
    const step = Math.max(1, Math.ceil(target / (duration / 16)));
    const interval = setInterval(() => {
        animatedStats.value[key] = Math.min(animatedStats.value[key] + step, target);
        if (animatedStats.value[key] >= target) clearInterval(interval);
    }, 16);
};

const imagesLoaded = ref({});
const handleImageLoad = (id) => {
    imagesLoaded.value[id] = true;
};

onMounted(() => {
    animateCount('penduduk', props.demografi?.total_penduduk ?? 0);
    animateCount('keluarga', props.demografi?.total_keluarga ?? 0);
    animateCount('pengajuan', props.layanan?.pengajuan_surat?.total ?? 0);
    animateCount('layanan', props.kategoriSurat?.length ?? 0);
});

const featureItems = [
    {
        title: "Layanan Mandiri",
        desc: "Warga dapat mengajukan berbagai jenis surat keterangan secara mandiri online.",
        icon: FileText,
        gradient: "from-blue-500/10 to-indigo-500/10 text-blue-600 border-blue-500/10",
        href: "/login"
    },
    {
        title: "Transparansi Data",
        desc: "Penyajian statistik demografi kependudukan desa secara real-time dan terbuka.",
        icon: Users,
        gradient: "from-blue-500/10 to-indigo-500/10 text-blue-600 border-blue-500/10",
        href: "/statistik"
    },
    {
        title: "Kabar Informasi",
        desc: "Dapatkan pengumuman kegiatan, berita terbaru, dan sosialisasi penting desa.",
        icon: Newspaper,
        gradient: "from-amber-500/10 to-orange-500/10 text-amber-600 border-amber-500/10",
        href: "/informasi"
    },
    {
        title: "Dukungan Cepat",
        desc: "Hubungi operator pelayanan langsung melalui WhatsApp untuk bantuan berkas.",
        icon: PhoneCall,
        gradient: "from-sky-500/10 to-cyan-500/10 text-sky-600 border-sky-500/10",
        href: "https://wa.me/6281234567890",
        external: true
    }
];

const aparaturList = [
    {
        name: "H. Syarifuddin",
        role: "Kepala Desa",
        photo: "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=600&auto=format&fit=crop"
    },
    {
        name: "Cut Rahmawati, S.Sos",
        role: "Sekretaris Desa",
        photo: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=600&auto=format&fit=crop"
    },
    {
        name: "Ahmad Faisal",
        role: "Kaur Keuangan",
        photo: "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=600&auto=format&fit=crop"
    }
];

const jsonLd = {
  "@context": "https://schema.org",
  "@type": "GovernmentOrganization",
  "name": "Pemerintahan Desa Udeung",
  "alternateName": "AvaraDesa-Udeung",
  "url": "https://udeung.desa.id",
  "logo": "https://udeung.desa.id/logo.svg",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Bandar Baru",
    "addressRegion": "Pidie Jaya, Aceh",
    "addressCountry": "ID",
    "postalCode": "24186"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+62-812-3456-7890",
    "contactType": "customer service"
  }
};
</script>

<template>
    <Head>
        <title>Beranda - Desa {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}, {{ $page.props.settings.provinsi }}</title>
        <meta name="description" :content="'Sistem Informasi Desa ' + $page.props.settings.nama_desa + ' Terpadu. Platform digital pelayanan mandiri surat keterangan warga secara online, transparan, cepat, dan terpercaya.'" />
        <meta name="keywords" :content="'Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten + ', ' + $page.props.settings.kecamatan + ', Desa Digital, AvaraDesa-' + $page.props.settings.nama_desa + ', Surat Online, Layanan Mandiri Desa'" />
        <meta property="og:title" :content="'Beranda - Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten" />
        <meta property="og:description" content="Platform administrasi desa modern untuk mempermudah permohonan surat keterangan warga secara daring, transparan, dan terintegrasi penuh." />
        <meta property="og:type" content="website" />
        <component :is="'script'" type="application/ld+json" v-html="JSON.stringify(jsonLd)"></component>
    </Head>

    <section class="relative min-h-[90vh] flex items-center justify-center bg-[#07090e] text-white overflow-hidden py-24 px-6">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-500/10 blur-[120px]" />
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-blue-500/10 blur-[120px]" />
            <img 
                :src="$page.props.settings.banner_desa || 'https://images.unsplash.com/photo-1604999333679-b86d54738315?q=80&w=1920&auto=format&fit=crop'" 
                alt="Landscape Desa" 
                class="size-full object-cover opacity-[0.45] scale-100 animate-subtle-zoom"
            />
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:32px_32px]" />
        </div>

        <div class="relative z-10 mx-auto max-w-5xl text-center space-y-8">
            <div class="flex flex-col items-center gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold bg-white/[0.04] border border-white/[0.08] backdrop-blur-md text-blue-400">
                    <Sparkles class="size-3.5 text-blue-400 animate-pulse" />
                    KBN Pilot Project Desa {{ $page.props.settings.nama_desa }}
                </span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white leading-[1.1] max-w-4xl mx-auto">
                Sistem Layanan Mandiri <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-blue-300 via-indigo-300 to-blue-400 bg-clip-text text-transparent">Digital Desa</span>
            </h1>
            
            <p class="text-base sm:text-xl text-slate-200 max-w-2xl mx-auto font-medium leading-relaxed">
                Platform administrasi desa modern untuk mempermudah permohonan surat keterangan warga secara daring, transparan, dan terintegrasi penuh.
            </p>
            
            <div class="pt-8 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <Link href="/login" class="inline-flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-all duration-300 px-8 py-4 text-sm font-semibold shadow-lg shadow-blue-500/20">
                    Ajukan Surat Online <ArrowUpRight class="size-4 ml-1.5" />
                </Link>
                <Link href="/profil" class="inline-flex items-center justify-center rounded-full bg-white/10 border border-white/20 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-white hover:bg-white/25 transition-all duration-300">
                    Pelajari Profil Desa
                </Link>
            </div>
        </div>
    </section>

    <section class="relative z-20 -mt-16 mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <template v-for="item in featureItems" :key="item.title">
                <a 
                    v-if="item.external"
                    :href="item.href"
                    target="_blank"
                    class="group bg-white border border-slate-200 p-6 rounded-2xl shadow-[0_4px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between hover:border-slate-350 transition duration-200"
                >
                    <div class="space-y-4">
                        <div class="size-11 rounded-xl flex items-center justify-center border" :class="item.gradient">
                            <component :is="item.icon" class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-1">
                                {{ item.title }} <ArrowUpRight class="size-3.5 text-slate-400 group-hover:text-slate-650 transition" />
                            </h3>
                            <p class="mt-2 text-xs text-slate-655 leading-relaxed font-medium">{{ item.desc }}</p>
                        </div>
                    </div>
                    <span class="mt-4 text-xs font-bold text-blue-600 inline-flex items-center gap-1">Hubungi Operator <ArrowRight class="size-3" /></span>
                </a>
                
                <Link 
                    v-else
                    :href="item.href"
                    class="group bg-white border border-slate-200 p-6 rounded-2xl shadow-[0_4px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between hover:border-slate-350 transition duration-200"
                >
                    <div class="space-y-4">
                        <div class="size-11 rounded-xl flex items-center justify-center border" :class="item.gradient">
                            <component :is="item.icon" class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-1">
                                {{ item.title }} <ArrowUpRight class="size-3.5 text-slate-400 group-hover:text-slate-655 transition" />
                            </h3>
                            <p class="mt-2 text-xs text-slate-655 leading-relaxed font-medium">{{ item.desc }}</p>
                        </div>
                    </div>
                    <span class="mt-4 text-xs font-bold text-blue-600 inline-flex items-center gap-1">Buka Halaman <ArrowRight class="size-3" /></span>
                </Link>
            </template>
        </div>
    </section>

    <section class="bg-white border-b border-slate-200 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 items-start">
                <div class="lg:col-span-4 space-y-4">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-blue-600">
                        Data Kependudukan
                    </span>
                    <h2 class="text-3xl font-normal text-slate-900 tracking-tight leading-tight">Udeung Dalam Angka</h2>
                    <p class="text-slate-700 text-sm leading-relaxed font-medium">
                        Kami menyajikan ringkasan data demografi dan layanan administrasi desa secara transparan untuk memantau perkembangan pembangunan desa.
                    </p>
                    <div class="pt-2">
                        <Link href="/statistik" class="inline-flex items-center justify-center rounded-full border border-slate-300 text-slate-800 hover:border-slate-450 bg-white transition duration-200 px-5 py-3 text-xs font-semibold">
                            Lihat Statistik Detail <ArrowRight class="size-3.5 ml-1.5" />
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-8 grid gap-6 sm:grid-cols-3">
                    <Link href="/statistik" class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-slate-300 hover:bg-slate-50 transition duration-200 space-y-2 block">
                        <span class="text-3xl font-normal text-[#202124] block">{{ animatedStats.penduduk }}</span>
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1">
                            Total Warga Aktif <ArrowUpRight class="size-3 text-slate-400" />
                        </h4>
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium">Penduduk terdata dan memiliki hak pelayanan digital mandiri.</p>
                    </Link>
                    <Link href="/statistik" class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-slate-300 hover:bg-slate-50 transition duration-200 space-y-2 block">
                        <span class="text-3xl font-normal text-[#202124] block">{{ animatedStats.keluarga }}</span>
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1">
                            Kepala Keluarga <ArrowUpRight class="size-3 text-slate-400" />
                        </h4>
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium">Jumlah kartu keluarga terdaftar di data administrasi desa.</p>
                    </Link>
                    <Link href="/statistik" class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-slate-300 hover:bg-slate-50 transition duration-200 space-y-2 block">
                        <span class="text-3xl font-normal text-[#202124] block">{{ animatedStats.pengajuan }}</span>
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1">
                            Surat Keterangan Terbit <ArrowUpRight class="size-3 text-slate-400" />
                        </h4>
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium">Total pengajuan persuratan mandiri warga yang selesai diproses.</p>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-b border-slate-200 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 items-start">
                <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-24">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-blue-600">
                        Pelayanan Publik
                    </span>
                    <h2 class="text-3xl font-normal text-slate-900 tracking-tight leading-tight">Jenis Layanan Surat Mandiri</h2>
                    <p class="text-slate-700 text-sm leading-relaxed font-medium">
                        Kami merangkum birokrasi konvensional menjadi beberapa klik praktis. Pilih berkas surat yang diperlukan dan lengkapi datanya langsung dari beranda warga Anda.
                    </p>
                    <div class="pt-2">
                        <Link href="/login" class="inline-flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200 px-6 py-3.5 text-xs font-semibold shadow-sm">
                            Masuk Portal Warga <ArrowUpRight class="size-3.5 ml-1" />
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-8 grid gap-px bg-slate-200 border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <Link 
                        v-for="kategori in kategoriSurat" 
                        :key="kategori.id"
                        href="/login"
                        class="bg-white p-6 flex flex-col justify-between hover:bg-slate-50/50 transition duration-150"
                    >
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md uppercase tracking-wider block w-fit">
                                    {{ kategori.kode_surat }}
                                </span>
                                <span class="text-xs text-blue-700 font-semibold inline-flex items-center gap-1 opacity-0 group-hover:opacity-100 transition duration-150">
                                    Ajukan <ArrowRight class="size-3" />
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-[#202124] leading-snug">{{ kategori.nama_surat }}</h3>
                                <p class="text-xs text-slate-655 leading-relaxed font-medium">Pengajuan berkas secara online dengan QR Code tanda tangan elektronik sah.</p>
                            </div>
                        </div>
                    </Link>

                    <div v-if="!kategoriSurat?.length" class="col-span-2 bg-white text-center py-12 text-slate-500 text-sm font-semibold">
                        Belum ada data kategori surat aktif.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-28 lg:px-8 bg-white">
        <div class="grid gap-12 lg:grid-cols-12 items-center">
            <div class="lg:col-span-5 relative order-2 lg:order-1">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/20 to-indigo-500/20 rounded-2xl filter blur-xl opacity-70 -z-10" />
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <img 
                        src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=1000&auto=format&fit=crop" 
                        alt="Citizen using portal" 
                        class="size-full object-cover aspect-[4/3]"
                    />
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6 order-1 lg:order-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800">
                    <Sparkles class="size-3.5" /> Alur Pengajuan
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">Proses Praktis 4 Langkah</h2>
                <p class="text-slate-700 leading-relaxed text-sm font-medium">
                    Kami memprioritaskan efisiensi penuh tanpa mengharuskan Anda mengantre lama di kantor balai desa.
                </p>

                <div class="grid gap-4 sm:grid-cols-2 pt-4">
                    <div class="p-5 bg-white border border-slate-200 rounded-2xl space-y-2">
                        <span class="text-2xl font-bold text-blue-700 block">01</span>
                        <h4 class="text-sm font-bold text-slate-900">Autentikasi NIK</h4>
                        <p class="text-[11px] text-slate-655 leading-relaxed font-semibold">Masuk ke portal dengan NIK warga dan kata sandi Anda.</p>
                    </div>
                    <div class="p-5 bg-white border border-slate-200 rounded-2xl space-y-2">
                        <span class="text-2xl font-bold text-blue-700 block">02</span>
                        <h4 class="text-sm font-bold text-slate-900">Input Data Isian</h4>
                        <p class="text-[11px] text-slate-655 leading-relaxed font-semibold">Isi formulir digital serta unggah berkas kelengkapan.</p>
                    </div>
                    <div class="p-5 bg-white border border-slate-200 rounded-2xl space-y-2">
                        <span class="text-2xl font-bold text-indigo-700 block">03</span>
                        <h4 class="text-sm font-bold text-slate-900">Verifikasi Berkas</h4>
                        <p class="text-[11px] text-slate-655 leading-relaxed font-semibold">Operator memproses data isian dan validasi berkas.</p>
                    </div>
                    <div class="p-5 bg-white border border-slate-200 rounded-2xl space-y-2">
                        <span class="text-2xl font-bold text-emerald-700 block">04</span>
                        <h4 class="text-sm font-bold text-slate-900">Unduh PDF Resmi</h4>
                        <p class="text-[11px] text-slate-655 leading-relaxed font-semibold">Surat yang terbit dilengkapi QR Code TTE sah.</p>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl bg-slate-50 border border-slate-200 p-5 flex flex-col sm:flex-row items-center gap-4">
                    <img 
                        src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop" 
                        alt="Operator Foto" 
                        class="size-12 rounded-full object-cover shrink-0"
                    />
                    <div class="grow text-center sm:text-left space-y-0.5">
                        <p class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Layanan WhatsApp Mandiri</p>
                        <p class="text-sm font-bold text-slate-800">Nadia Safira (Operator Desa)</p>
                        <p class="text-xs text-blue-800 font-bold">0812-3456-7890</p>
                    </div>
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-750 text-white font-bold text-xs py-2.5 px-5 transition-all shadow-sm">
                        Hubungi Operator
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 border-t border-slate-200 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-3 mb-16">
                <span class="text-xs font-bold tracking-widest text-blue-600 uppercase">Kampung Bebas Narkoba (KBN)</span>
                <h2 class="text-3xl font-normal text-slate-900 tracking-tight leading-tight">Komitmen Lingkungan Udeung</h2>
                <p class="text-slate-700 max-w-xl mx-auto text-sm leading-relaxed font-medium">
                    Sebagai wilayah percontohan anti-narkoba di Kabupaten Pidie Jaya, kami bersinergi aktif bersama warga mempertahankan KBN.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <Link href="/profil" class="bg-white p-6 rounded-2xl border border-slate-200 hover:border-slate-350 transition duration-200 flex flex-col justify-between min-h-[180px]">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#D93025] bg-red-50 px-2.5 py-1 rounded-md uppercase tracking-wider">
                            Pencegahan
                        </span>
                        <h3 class="text-sm font-bold text-[#202124] flex items-center gap-1">
                            Sosialisasi Pemuda <ArrowUpRight class="size-3 text-slate-400" />
                        </h3>
                        <p class="text-xs text-slate-655 leading-relaxed font-medium">Penyuluhan berkala mengenai bahaya zat terlarang yang menyasar kelompok pemuda desa.</p>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-4 flex items-center gap-1">Detail di Profil Desa <ArrowRight class="size-3" /></span>
                </Link>
                <Link href="/profil" class="bg-white p-6 rounded-2xl border border-slate-200 hover:border-slate-350 transition duration-200 flex flex-col justify-between min-h-[180px]">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#1E8E3E] bg-green-50 px-2.5 py-1 rounded-md uppercase tracking-wider">
                            Partisipasi
                        </span>
                        <h3 class="text-sm font-bold text-[#202124] flex items-center gap-1">
                            Pengawasan Warga <ArrowUpRight class="size-3 text-slate-400" />
                        </h3>
                        <p class="text-xs text-slate-655 leading-relaxed font-medium">Masyarakat aktif berperan sebagai lini pengaman utama dalam melaporkan peredaran narkoba.</p>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-4 flex items-center gap-1">Detail di Profil Desa <ArrowRight class="size-3" /></span>
                </Link>
                <Link href="/profil" class="bg-white p-6 rounded-2xl border border-slate-200 hover:border-slate-350 transition duration-200 flex flex-col justify-between min-h-[180px]">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md uppercase tracking-wider">
                            Sinergitas
                        </span>
                        <h3 class="text-sm font-bold text-[#202124] flex items-center gap-1">
                            Mitra BNK & Polres <ArrowUpRight class="size-3 text-slate-400" />
                        </h3>
                        <p class="text-xs text-slate-655 leading-relaxed font-medium">Kolaborasi terarah bersama jajaran Kepolisian Resor dan Badan Narkotika Kabupaten.</p>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-4 flex items-center gap-1">Detail di Profil Desa <ArrowRight class="size-3" /></span>
                </Link>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-28 lg:px-8 border-t border-slate-200 bg-white">
        <div class="text-center space-y-3 mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                <Users class="size-3.5" /> Struktur Pemerintahan
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">Pemerintahan Desa</h2>
            <p class="text-slate-755 max-w-xl mx-auto text-sm leading-relaxed font-bold">
                Jajaran aparatur desa Udeung yang berfokus melayani kepentingan administrasi dan pembangunan desa.
            </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div 
                v-for="aparat in aparaturList" 
                :key="aparat.name"
                class="bg-slate-50/50 rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-300"
            >
                <div class="aspect-[4/3] w-full overflow-hidden bg-slate-50">
                    <img 
                        :src="aparat.photo" 
                        :alt="aparat.name"
                        class="size-full object-cover"
                    />
                </div>
                <div class="p-6 text-center space-y-2 bg-white border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ aparat.name }}</h3>
                    <span class="inline-block text-[10px] font-bold text-blue-900 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">{{ aparat.role }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 border-t border-slate-200 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 items-center">
                <div class="lg:col-span-5 space-y-5">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-blue-600">
                        Orbitasi Wilayah
                    </span>
                    <h2 class="text-3xl font-normal text-slate-900 tracking-tight leading-tight">Letak & Akses Desa</h2>
                    <p class="text-slate-700 text-sm leading-relaxed font-medium">
                        Desa {{ $page.props.settings.nama_desa }} terletak di Kecamatan {{ $page.props.settings.kecamatan }}, berjarak sekitar 3.5 km dari simpang pusat kecamatan Lueng Putu. Berbatasan langsung dengan Desa Ara di bagian utara melalui jembatan gantung Garuda.
                    </p>
                    <div class="space-y-2 pt-2 text-xs text-slate-800 font-bold">
                        <p>• Koordinat Sektor: 5.277732, 96.102347</p>
                        <p>• Sektor Komoditas: Padi Sawah, Jagung, Kakao</p>
                    </div>
                </div>

                <div @mouseleave="mapActive = false" class="lg:col-span-7 rounded-2xl border border-slate-200 overflow-hidden relative h-80 shadow-sm bg-white p-2 group/map">
                    <iframe 
                        src="https://www.openstreetmap.org/export/embed.html?bbox=96.0900%2C5.2650%2C96.1150%2C5.2900&amp;layer=mapnik&amp;marker=5.2777%2C96.1023" 
                        class="size-full border-0 absolute inset-0 rounded-xl transition duration-300"
                        :class="{ 'pointer-events-none': !mapActive }"
                        allowfullscreen="" 
                        loading="lazy"
                    ></iframe>
                    <div v-if="!mapActive" @click="mapActive = true" class="absolute inset-0 bg-slate-900/5 cursor-pointer flex items-center justify-center transition duration-300 hover:bg-slate-900/10">
                        <span class="bg-slate-900/80 backdrop-blur-sm text-white text-xs font-bold px-4 py-2 rounded-full shadow-md">
                            Klik untuk Interaksi Peta
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-t border-slate-200 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800">
                        <Newspaper class="size-3.5" /> Kabar Desa
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">Berita & Informasi Terbaru</h2>
                </div>
                <AppButton href="/informasi" variant="outline" class="rounded-full self-start shrink-0">
                    Lihat Semua Berita
                </AppButton>
            </div>

            <div v-if="berita?.length" class="grid gap-8 md:grid-cols-3">
                <article 
                    v-for="item in berita" 
                    :key="item.id"
                    class="group flex flex-col justify-between bg-slate-50/50 rounded-2xl border border-slate-200 overflow-hidden hover:border-slate-350 transition duration-200"
                >
                    <div>
                        <div class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-blue-500/20 via-indigo-500/10 to-blue-600/20 flex items-center justify-center border-b border-slate-150">
                            <div v-if="item.cover_image && !imagesLoaded[item.id]" class="absolute inset-0 animate-pulse bg-slate-200/60" />
                            <img 
                                v-if="item.cover_image" 
                                :src="item.cover_image" 
                                :alt="item.judul"
                                class="w-full h-full object-cover transition-opacity duration-300"
                                :class="imagesLoaded[item.id] ? 'opacity-100' : 'opacity-0'"
                                @load="handleImageLoad(item.id)"
                            />
                            <div v-else class="flex flex-col items-center justify-center text-blue-800/60 p-4 text-center">
                                <Newspaper class="size-10 mb-2 stroke-[1.5]" />
                                <span class="text-xs font-semibold tracking-wide uppercase opacity-70">AvaraDesa Kabar</span>
                            </div>
                            
                            <div class="absolute bottom-3 left-3">
                                <span class="text-[10px] font-bold tracking-wider text-blue-800 uppercase bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm">
                                    {{ item.kategori }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-3 text-[10px] text-slate-655 font-bold">
                                <span class="flex items-center gap-1">
                                    <Clock class="size-3" />
                                    {{ new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                </span>
                                <span class="size-1 rounded-full bg-slate-400"></span>
                                <span class="flex items-center gap-1">
                                    <User class="size-3" />
                                    Admin Desa
                                </span>
                            </div>
                            
                            <h3 class="text-base font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-teal-700 transition-colors duration-300">
                                <Link :href="`/informasi/${item.slug}`">{{ item.judul }}</Link>
                            </h3>
                            
                            <p class="text-xs text-slate-700 line-clamp-3 leading-relaxed font-medium">
                                {{ stripHtml(item.konten) }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="px-6 pb-6 pt-2">
                        <Link 
                            :href="`/informasi/${item.slug}`" 
                            class="text-blue-700 font-bold flex items-center gap-1 text-xs hover:text-blue-800 transition duration-150"
                        >
                            Baca Selengkapnya <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                        </Link>
                    </div>
                </article>
            </div>
            <EmptyState v-else class="mt-6 bg-white" title="Belum ada informasi" message="Berita desa akan tampil di sini setelah dipublikasikan." :icon="Newspaper" />
        </div>
    </section>
</template>

<style>
.animate-subtle-zoom {
    animation: subtleZoom 25s infinite alternate ease-in-out;
}

@keyframes subtleZoom {
    0% { transform: scale(1.02) rotate(0.1deg); }
    100% { transform: scale(1.06) rotate(-0.1deg); }
}
</style>
