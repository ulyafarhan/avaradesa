<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import AppCard from '../../Components/AppCard.vue';
import { 
    Target, 
    Eye, 
    Compass, 
    History, 
    Map, 
    CheckCircle2, 
    UserCheck,
    MapPin,
    Navigation,
    Layers,
    ShieldAlert,
    BookOpen,
    GraduationCap,
    TrendingUp,
    Info
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });
defineProps({ perangkat: Array });
const mapActive = ref(false);

const getAvatar = (orang) => {
    if (orang.foto) {
        return orang.foto;
    }
    const jabatan = orang.jabatan;
    if (jabatan.includes('Kepala')) {
        return "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop";
    } else if (jabatan.includes('Sekretaris')) {
        return "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop";
    }
    return "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=200&auto=format&fit=crop";
};
</script>

<template>
    <Head>
        <title>Profil Desa - {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}</title>
        <meta name="description" :content="'Profil resmi Desa ' + $page.props.settings.nama_desa + ', Kecamatan ' + $page.props.settings.kecamatan + ', ' + $page.props.settings.kabupaten + '. Informasi visi, misi, letak geografis, dan jajaran aparatur desa.'" />
        <meta name="keywords" :content="'Profil Desa ' + $page.props.settings.nama_desa + ', Struktur Organisasi Desa, Letak Geografis, Visi Misi Desa'" />
        <meta property="og:title" :content="'Profil Desa - ' + $page.props.settings.nama_desa" />
        <meta property="og:description" :content="'Profil resmi Desa ' + $page.props.settings.nama_desa + '. Informasi visi, misi, letak geografis, dan jajaran aparatur desa.'" />
    </Head>

    <header class="bg-white border-b border-gray-200 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F0FE] text-[#1A73E8] uppercase tracking-wider">
                    <Info class="size-3.5" /> Profil & Lembaga Administrasi
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-normal text-[#202124] tracking-tight leading-tight">
                    Desa {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kecamatan }}
                </h1>
                
                <p class="text-base sm:text-lg text-[#5F6368] font-normal leading-relaxed">
                    Kabupaten Pidie Jaya, Provinsi Aceh — Kode Wilayah Kemendagri: 11.18.06.2017. 
                    Ditetapkan resmi sebagai Kampung Bebas Narkoba (KBN) Percontohan.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8 bg-white">
        <div class="grid gap-12 lg:grid-cols-2">
            <div class="bg-[#F8F9FA] rounded-2xl border border-gray-200 p-8 sm:p-10 flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-[#E8F0FE] text-[#1A73E8]">
                        <Eye class="size-5" />
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#1A73E8] block">Visi Desa</span>
                    <h2 class="text-2xl sm:text-3xl font-normal text-[#202124] leading-snug">
                        "Mewujudkan Desa yang Mandiri, Sejahtera, Transparan, Bebas Narkoba, dan Religius berbasis Pelayanan Digital Prima."
                    </h2>
                </div>
                <div class="pt-6 border-t border-gray-200 mt-8 flex items-center gap-2 text-xs text-[#5F6368]">
                    <CheckCircle2 class="size-4 text-emerald-600" />
                    <span>RPJM Desa Periode 2026 - 2031</span>
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <Target class="size-5" />
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700 block">Misi Desa</span>
                    <h2 class="text-2xl font-normal text-[#202124]">Agenda Pembangunan & Sosial</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex gap-4 items-start p-5 rounded-2xl border border-gray-200 bg-white">
                        <span class="flex size-6 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs shrink-0 mt-0.5">1</span>
                        <div class="text-xs text-[#5F6368] leading-relaxed font-medium">
                            <strong class="text-[#202124] text-sm block mb-1 font-bold">Tata Kelola & Digitalisasi Administrasi</strong>
                            Mengoptimalkan pelayanan administrasi kependudukan desa secara cepat dan akuntabel melalui Portal Mandiri Warga.
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-5 rounded-2xl border border-gray-200 bg-white">
                        <span class="flex size-6 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs shrink-0 mt-0.5">2</span>
                        <div class="text-xs text-[#5F6368] leading-relaxed font-medium">
                            <strong class="text-[#202124] text-sm block mb-1 font-bold">Imunitas Bahaya Narkotika (KBN)</strong>
                            Membangun daya tangkal dan pengawasan sosial pemuda secara aktif guna mempertahankan status Kampung Bebas Narkoba.
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-5 rounded-2xl border border-gray-200 bg-white">
                        <span class="flex size-6 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs shrink-0 mt-0.5">3</span>
                        <div class="text-xs text-[#5F6368] leading-relaxed font-medium">
                            <strong class="text-[#202124] text-sm block mb-1 font-bold">Ekonomi Agraris & Kemitraan UMKM</strong>
                            Meningkatkan produktivitas komoditas padi sawah, jagung, kakao, serta kemasan produk rumah tangga (kue tradisional Aceh).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#F8F9FA] border-y border-gray-200 py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <ShieldAlert class="size-5 text-[#D93025]" />
                        <span class="text-xs font-bold tracking-widest text-[#D93025] uppercase">Percontohan Quick Wins Presisi</span>
                    </div>
                    <h2 class="text-3xl font-normal text-[#202124] tracking-tight leading-tight">Benteng Pertahanan Anti-Narkoba Pidie Jaya</h2>
                    <p class="text-slate-700 leading-relaxed text-sm font-medium">
                        Desa {{ $page.props.settings.nama_desa }} diresmikan secara formal sebagai <strong class="text-[#202124] font-bold">Kampung Bebas Narkoba (KBN)</strong> pada <strong class="text-[#202124] font-bold">Senin, 14 Oktober 2024</strong> oleh Pj Bupati Pidie Jaya bersama Kapolres Pidie Jaya dan Kepala BNK.
                    </p>
                    <p class="text-slate-700 leading-relaxed text-sm font-medium">
                        Penunjukan ini menjadikannya proyek percontohan sekaligus tolak ukur pemberantasan narkotika tingkat desa bagi <strong class="text-[#202124] font-bold">221 desa</strong> lainnya di seluruh Kabupaten Pidie Jaya.
                    </p>
                    <div class="grid grid-cols-3 gap-4 pt-4 text-center">
                        <div class="p-4 bg-white rounded-xl border border-gray-200">
                            <p class="text-2xl font-normal text-[#1A73E8]">221</p>
                            <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Desa Rujukan</p>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-gray-200">
                            <p class="text-2xl font-normal text-[#1A73E8]">100%</p>
                            <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Komitmen Warga</p>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-gray-200">
                            <p class="text-2xl font-normal text-[#1A73E8]">Polres</p>
                            <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Sinergi BNK</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-white border border-gray-200 flex gap-4">
                        <CheckCircle2 class="size-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-[#202124] text-sm">Deklarasi Bersama Melawan</h4>
                            <p class="text-xs text-[#5F6368] mt-1 leading-relaxed">Penandatanganan pakta komitmen seluruh tokoh adat, pemuda, dan aparat keamanan.</p>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-gray-200 flex gap-4">
                        <CheckCircle2 class="size-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-[#202124] text-sm">Edukasi Preemtif & Imunitas Bahaya</h4>
                            <p class="text-xs text-[#5F6368] mt-1 leading-relaxed">Sosialisasi bahaya narkoba menyasar pemuda untuk membentuk kesadahu internal.</p>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-gray-200 flex gap-4">
                        <CheckCircle2 class="size-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-[#202124] text-sm">Pengawasan Sosial Terpadu</h4>
                            <p class="text-xs text-[#5F6368] mt-1 leading-relaxed">Masyarakat aktif bertindak sebagai mata dan telinga deteksi dini peredaran gelap.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <History class="size-5 text-[#1A73E8]" />
                        <span class="text-xs font-bold tracking-widest text-[#1A73E8] uppercase">Sejarah & Infrastruktur</span>
                    </div>
                    <h2 class="text-3xl font-normal text-[#202124] tracking-tight leading-tight">Sejarah Berdiri & Konektivitas Garuda</h2>
                    <div class="space-y-4 text-sm text-slate-700 leading-relaxed font-medium">
                        <p>
                            Desa {{ $page.props.settings.nama_desa }} didirikan sejak masa lampau di {{ $page.props.settings.kecamatan }}. Nama wilayah disematkan karena letak historis dan geografisnya yang melimpah akan kekayaan alam lokal.
                        </p>
                        <p>
                            Kini, aksesibilitas warga semakin kuat dengan hadirnya jembatan gantung Garuda yang melintasi Sungai Lueng Putu, menghubungkan langsung Desa {{ $page.props.settings.nama_desa }} dengan Desa Ara. Jembatan gantung ini menjadi urat nadi perekonomian, peribadahan santri menuju Pesantren Raudhatul Ulum di Desa Ara, serta mobilitas anak-anak menuju SD Negeri Neulop Mate yang telah mendidik anak-anak desa sejak tahun 1977.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold bg-gray-50 px-3.5 py-1.5 rounded-full border border-gray-200">
                            <BookOpen class="size-4 text-[#1A73E8]" /> SDN Neulop Mate (1977)
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold bg-gray-50 px-3.5 py-1.5 rounded-full border border-gray-200">
                            <Compass class="size-4 text-[#1A73E8]" /> Jembatan Garuda
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold bg-gray-50 px-3.5 py-1.5 rounded-full border border-gray-200">
                            <GraduationCap class="size-4 text-[#1A73E8]" /> Pesantren Raudhatul Ulum
                        </span>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
                    <img 
                        src="https://images.unsplash.com/photo-1598970434795-0c54fe7c0648?q=80&w=600&auto=format&fit=crop" 
                        alt="Landscape Udeung" 
                        class="w-full h-full object-cover aspect-[4/3]"
                    />
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#F8F9FA] border-t border-gray-200 py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-3 mb-16">
                <span class="text-xs font-bold tracking-widest text-[#1A73E8] uppercase">Struktur Organisasi</span>
                <h2 class="text-3xl font-normal text-[#202124] tracking-tight leading-tight">Pemerintahan Desa {{ $page.props.settings.nama_desa }}</h2>
                <p class="text-[#5F6368] max-w-xl mx-auto text-sm leading-relaxed">
                    Jajaran aparatur desa yang bertanggung jawab penuh dalam tata kelola keamanan, ekonomi agraris, serta digitalisasi berkas warga.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="orang in perangkat" 
                    :key="orang.jabatan"
                    class="flex flex-col items-center text-center p-8 border border-gray-200 bg-white rounded-2xl"
                >
                    <img 
                        :src="getAvatar(orang)" 
                        :alt="orang.nama"
                        class="size-20 rounded-full object-cover border border-gray-200 mb-5 shrink-0"
                    />
                    <span class="text-[10px] font-bold text-[#1A73E8] bg-[#E8F0FE] px-3 py-1 rounded-full uppercase tracking-wider">{{ orang.jabatan }}</span>
                    <h3 class="mt-3 text-base font-bold text-[#202124]">{{ orang.nama }}</h3>
                    <p class="mt-2 text-xs text-[#5F6368] max-w-[200px] leading-relaxed">Aparatur Pemerintahan Desa {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kecamatan }}, {{ $page.props.settings.kabupaten }}</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="bg-white border-t border-gray-200 py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-3 mb-12">
                <span class="text-xs font-bold tracking-widest text-[#1A73E8] uppercase">Kondisi Geografis</span>
                <h2 class="text-3xl font-normal text-[#202124] tracking-tight leading-tight">Letak Astronomis & Batas Sektor</h2>
                <p class="text-[#5F6368] max-w-xl mx-auto text-sm leading-relaxed">
                    Terletak pada koordinat 5.277732, 96.102347, berjarak sekitar 3.5 km dari simpang pusat kecamatan Lueng Putu.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-10">
                <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 shrink-0">
                        <TrendingUp class="size-5" />
                    </div>
                    <div>
                        <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Komoditas Lahan</p>
                        <p class="text-sm font-bold text-[#202124]">Padi, Jagung, Kakao</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 shrink-0">
                        <Navigation class="size-5" />
                    </div>
                    <div>
                        <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Batas Utara</p>
                        <p class="text-sm font-bold text-[#202124]">Desa Ara (Jembatan)</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 shrink-0">
                        <Navigation class="size-5 transform rotate-180" />
                    </div>
                    <div>
                        <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Batas Selatan</p>
                        <p class="text-sm font-bold text-[#202124]">Desa Blang Baro</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 shrink-0">
                        <Layers class="size-5" />
                    </div>
                    <div>
                        <p class="text-[9px] text-[#5F6368] uppercase font-bold tracking-wider">Orbitasi Simpang</p>
                        <p class="text-sm font-bold text-[#202124]">3.5 Km ke Lueng Putu</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm overflow-hidden group/map">
                <h3 class="text-base font-bold text-[#202124] mb-4">Peta Administrasi Desa (OpenStreetMap)</h3>
                <div @mouseleave="mapActive = false" class="w-full h-80 rounded-xl overflow-hidden border border-gray-200 relative">
                    <iframe 
                        src="https://www.openstreetmap.org/export/embed.html?bbox=96.0900%2C5.2650%2C96.1150%2C5.2900&amp;layer=mapnik&amp;marker=5.2777%2C96.1023" 
                        class="size-full border-0 absolute inset-0 transition duration-300"
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
</template>
