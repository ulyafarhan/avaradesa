<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { 
    Eye, 
    Compass, 
    History, 
    CheckCircle2, 
    Navigation,
    Layers,
    BookOpen,
    GraduationCap,
    TrendingUp
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

    <header class="bg-white pt-32 pb-24 border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-4xl space-y-8">
                <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-4 border-l-4 border-amber-600 pl-4 font-sans">
                    Profil & Lembaga Administrasi
                </span>
                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-slate-900 tracking-tight leading-[1.05] font-heading">
                    Desa {{ $page.props.settings.nama_desa }},<br/><span class="italic text-amber-600">{{ $page.props.settings.kecamatan }}.</span>
                </h1>
                
                <p class="text-xl text-slate-600 font-sans leading-relaxed max-w-2xl border-l-2 border-slate-200 pl-4">
                    Kabupaten {{ $page.props.settings.kabupaten }}, Provinsi {{ $page.props.settings.provinsi }} — Kode Wilayah Kemendagri: 11.18.06.2017. 
                    Ditetapkan resmi sebagai Kampung Bebas Narkoba (KBN) Percontohan.
                </p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-6 py-32 lg:px-8 bg-white">
        <div class="grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-6 bg-slate-950 text-white rounded-[2rem] p-12 flex flex-col justify-between shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-amber-500/10 rounded-full blur-[80px]"></div>
                <div class="space-y-8 relative z-10">
                    <div class="flex size-14 items-center justify-center rounded-2xl bg-white/10 text-white backdrop-blur-sm border border-white/20">
                        <Eye class="size-6" />
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/60 block font-sans">Visi Desa</span>
                    <h2 class="text-3xl sm:text-4xl font-heading font-medium leading-[1.2]">
                        "{{ $page.props.settings.visi }}"
                    </h2>
                </div>
                <div class="pt-8 border-t border-white/20 mt-12 flex items-center gap-3 text-xs font-bold tracking-widest uppercase text-white/80 relative z-10 font-sans">
                    <CheckCircle2 class="size-4 text-amber-500" />
                    <span>RPJM Desa 2026 - 2031</span>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-10">
                <div class="space-y-4">
                    <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase mb-4 border-l-4 border-amber-600 pl-4 font-sans">
                        Misi Desa
                    </span>
                    <h2 class="text-4xl font-heading font-bold text-slate-900">Agenda<br/><span class="italic text-amber-600">Pembangunan.</span></h2>
                </div>
                
                <div class="space-y-6">
                    <div v-for="(m, i) in $page.props.settings.misi" :key="i" class="flex gap-6 items-start group">
                        <span class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-900 font-heading font-bold text-xl shrink-0 transition-all group-hover:bg-amber-100 group-hover:text-amber-700 duration-500">{{ i + 1 }}</span>
                        <div :class="['text-sm text-slate-600 leading-relaxed font-sans pt-3', i !== $page.props.settings.misi.length - 1 ? 'border-b border-slate-200 pb-6' : 'pb-6']">
                            <strong v-if="m.misi_item.includes(':')" class="text-slate-900 text-lg block mb-2 font-heading font-bold">{{ m.misi_item.split(':')[0] }}</strong>
                            {{ m.misi_item.includes(':') ? m.misi_item.split(':')[1].trim() : m.misi_item }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 border-y border-slate-200 py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <span class="block text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase font-sans border-l-4 border-amber-600 pl-4">
                        Percontohan Quick Wins Presisi
                    </span>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight leading-tight font-heading">Benteng Pertahanan Anti-Narkoba {{ $page.props.settings.kabupaten }}</h2>
                    <p class="text-slate-600 leading-relaxed text-sm font-sans">
                        Desa {{ $page.props.settings.nama_desa }} diresmikan secara formal sebagai <strong class="text-slate-900 font-bold">Kampung Bebas Narkoba (KBN)</strong> pada <strong class="text-slate-900 font-bold">{{ $page.props.settings.kbn_tanggal_resmi }}</strong> oleh Pj Bupati {{ $page.props.settings.kabupaten }} bersama Kapolres {{ $page.props.settings.kabupaten }} dan Kepala BNK.
                    </p>
                    <p class="text-slate-600 leading-relaxed text-sm font-sans">
                        Penunjukan ini menjadikannya proyek percontohan sekaligus tolak ukur pemberantasan narkotika tingkat desa bagi <strong class="text-slate-900 font-bold">{{ $page.props.settings.kbn_jumlah_desa }}</strong> lainnya di seluruh Kabupaten {{ $page.props.settings.kabupaten }}.
                    </p>
                    <div class="grid grid-cols-3 gap-4 pt-4 text-center">
                        <div class="p-4 bg-white rounded-xl border border-slate-200">
                            <p class="text-3xl font-bold text-amber-600 font-heading">{{ parseInt($page.props.settings.kbn_jumlah_desa) || 221 }}</p>
                            <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider mt-2 font-sans">Desa Rujukan</p>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-slate-200">
                            <p class="text-3xl font-bold text-amber-600 font-heading">100%</p>
                            <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider mt-2 font-sans">Komitmen Warga</p>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-slate-200">
                            <p class="text-3xl font-bold text-amber-600 font-heading">Polres</p>
                            <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider mt-2 font-sans">Sinergi BNK</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="p-6 rounded-xl bg-white border border-slate-200 flex gap-4 transition-colors hover:border-amber-200">
                        <CheckCircle2 class="size-6 text-amber-500 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 text-base font-heading">Deklarasi Bersama Melawan</h4>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed font-sans">Penandatanganan pakta komitmen seluruh tokoh adat, pemuda, dan aparat keamanan.</p>
                        </div>
                    </div>
                    <div class="p-6 rounded-xl bg-white border border-slate-200 flex gap-4 transition-colors hover:border-amber-200">
                        <CheckCircle2 class="size-6 text-amber-500 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 text-base font-heading">Edukasi Preemtif & Imunitas Bahaya</h4>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed font-sans">Sosialisasi bahaya narkoba menyasar pemuda untuk membentuk kesadaran internal.</p>
                        </div>
                    </div>
                    <div class="p-6 rounded-xl bg-white border border-slate-200 flex gap-4 transition-colors hover:border-amber-200">
                        <CheckCircle2 class="size-6 text-amber-500 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 text-base font-heading">Pengawasan Sosial Terpadu</h4>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed font-sans">Masyarakat aktif bertindak sebagai mata dan telinga deteksi dini peredaran gelap.</p>
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
                        <History class="size-5 text-amber-600" />
                        <span class="text-xs font-bold tracking-widest text-amber-600 uppercase font-sans">Sejarah & Infrastruktur</span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight leading-tight font-heading">Sejarah Berdiri & Infrastruktur</h2>
                    <div class="space-y-4 text-base text-slate-600 leading-relaxed font-sans border-l-2 border-slate-100 pl-4 whitespace-pre-line">
                        {{ $page.props.settings.sejarah_desa }}
                    </div>
                    <div class="flex flex-wrap gap-3 pt-4">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-900 font-semibold bg-slate-50 px-4 py-2 rounded-lg border border-slate-200 font-sans">
                            <BookOpen class="size-4 text-amber-600" /> SDN Neulop Mate (1977)
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-slate-900 font-semibold bg-slate-50 px-4 py-2 rounded-lg border border-slate-200 font-sans">
                            <Compass class="size-4 text-amber-600" /> Jembatan Garuda
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-slate-900 font-semibold bg-slate-50 px-4 py-2 rounded-lg border border-slate-200 font-sans">
                            <GraduationCap class="size-4 text-amber-600" /> Pesantren Raudhatul Ulum
                        </span>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-900/5">
                    <img 
                        src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?q=80&w=600&auto=format&fit=crop"
                        :alt="'Landscape ' + $page.props.settings.nama_desa" 
                        class="w-full h-full object-cover aspect-[4/3] transition-transform duration-[5000ms] hover:scale-105"
                    />
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 border-t border-slate-200 py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-6 mb-20 max-w-3xl mx-auto">
                <span class="text-xs font-bold tracking-[0.2em] text-amber-600 uppercase font-sans">Struktur Organisasi</span>
                <h2 class="text-5xl lg:text-6xl font-heading font-bold text-slate-900 tracking-tight leading-[1.1]">
                    Pemerintahan<br/><span class="italic text-amber-600">Desa {{ $page.props.settings.nama_desa }}.</span>
                </h2>
                <p class="text-slate-500 text-lg leading-relaxed font-sans">
                    Jajaran aparatur desa yang bertanggung jawab penuh dalam tata kelola keamanan, ekonomi agraris, serta digitalisasi berkas warga.
                </p>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="(orang, index) in perangkat" 
                    :key="orang.jabatan"
                    :class="[
                        index === 0 ? 'sm:col-span-2 lg:col-span-3 bg-slate-950 text-white p-12 lg:p-16 flex flex-col md:flex-row items-center gap-12' : 'bg-white text-slate-900 p-8 flex flex-col items-center text-center',
                        'rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-500 group'
                    ]"
                >
                    <div :class="[index === 0 ? 'size-40 md:size-56' : 'size-32', 'shrink-0 overflow-hidden rounded-full border-4 border-slate-100 relative shadow-2xl']">
                        <img 
                            :src="getAvatar(orang)" 
                            :alt="orang.nama"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                    </div>
                    
                    <div :class="index === 0 ? 'text-center md:text-left space-y-4' : 'space-y-3 mt-6'">
                        <span :class="[
                            index === 0 ? 'text-amber-400 bg-white/10 border-white/20' : 'text-slate-900 bg-slate-50 border-slate-200',
                            'inline-flex text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-widest border font-sans'
                        ]">
                            {{ orang.jabatan }}
                        </span>
                        <h3 :class="[index === 0 ? 'text-3xl md:text-4xl' : 'text-xl', 'font-heading font-bold leading-tight']">{{ orang.nama }}</h3>
                        <p :class="[index === 0 ? 'text-white/70 text-base max-w-md' : 'text-slate-500 text-sm', 'leading-relaxed font-sans']">
                            Aparatur Pemerintahan Desa {{ $page.props.settings.nama_desa }}, Kecamatan {{ $page.props.settings.kecamatan }}, menjalankan dedikasi penuh untuk layanan publik prima.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="bg-white border-t border-slate-200 py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-12">
                <span class="text-xs font-bold tracking-widest text-amber-600 uppercase font-sans">Kondisi Geografis</span>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight leading-tight font-heading">Letak Astronomis & Batas Sektor</h2>
                <p class="text-slate-500 max-w-xl mx-auto text-base leading-relaxed font-sans">
                    Terletak pada koordinat {{ $page.props.settings.geo_koordinat }}
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-12">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col items-center text-center gap-4 transition-colors hover:border-amber-300">
                    <div class="flex size-14 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 text-amber-600 shrink-0">
                        <TrendingUp class="size-6" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 font-sans">Komoditas Lahan</p>
                        <p class="text-base font-bold text-slate-900 font-heading">{{ $page.props.settings.geo_komoditas }}</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col items-center text-center gap-4 transition-colors hover:border-amber-300">
                    <div class="flex size-14 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 text-amber-600 shrink-0">
                        <Navigation class="size-6" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 font-sans">Batas Utara</p>
                        <p class="text-base font-bold text-slate-900 font-heading">{{ $page.props.settings.geo_batas_utara }}</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col items-center text-center gap-4 transition-colors hover:border-amber-300">
                    <div class="flex size-14 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 text-amber-600 shrink-0">
                        <Navigation class="size-6 transform rotate-180" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 font-sans">Batas Selatan</p>
                        <p class="text-base font-bold text-slate-900 font-heading">{{ $page.props.settings.geo_batas_selatan }}</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col items-center text-center gap-4 transition-colors hover:border-amber-300">
                    <div class="flex size-14 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 text-amber-600 shrink-0">
                        <Layers class="size-6" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 font-sans">Orbitasi Simpang</p>
                        <p class="text-base font-bold text-slate-900 font-heading">{{ $page.props.settings.geo_orbitasi }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6 shadow-xl shadow-slate-900/5 overflow-hidden group/map">
                <h3 class="text-xl font-bold text-slate-900 mb-6 font-heading text-center">Peta Administrasi Desa (OpenStreetMap)</h3>
                <div @mouseleave="mapActive = false" class="w-full h-[400px] rounded-2xl overflow-hidden border border-slate-300 relative shadow-inner">
                    <iframe 
                        :src="'https://www.openstreetmap.org/export/embed.html?bbox=' + (parseFloat($page.props.settings.geo_koordinat.split(',')[1])-0.0123) + '%2C' + (parseFloat($page.props.settings.geo_koordinat.split(',')[0])-0.0127) + '%2C' + (parseFloat($page.props.settings.geo_koordinat.split(',')[1])+0.0127) + '%2C' + (parseFloat($page.props.settings.geo_koordinat.split(',')[0])+0.0123) + '&amp;layer=mapnik&amp;marker=' + $page.props.settings.geo_koordinat.split(',')[0] + '%2C' + $page.props.settings.geo_koordinat.split(',')[1]" 
                        class="size-full border-0 absolute inset-0 transition duration-300"
                        :class="{ 'pointer-events-none': !mapActive }"
                        allowfullscreen="" 
                        loading="lazy"
                    ></iframe>
                    <div v-if="!mapActive" @click="mapActive = true" class="absolute inset-0 bg-slate-900/5 cursor-pointer flex items-center justify-center transition duration-300 hover:bg-slate-900/10">
                        <span class="bg-slate-900/90 backdrop-blur-md text-white text-sm font-bold uppercase tracking-wider px-6 py-3 rounded-full shadow-2xl border border-slate-700 font-sans transition-transform hover:scale-105">
                            Klik untuk Interaksi Peta
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
