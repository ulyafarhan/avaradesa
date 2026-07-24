<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import HomeHero from './Partials/HomeHero.vue';
import HomeFeatures from './Partials/HomeFeatures.vue';
import HomeStatistics from './Partials/HomeStatistics.vue';
import HomeServices from './Partials/HomeServices.vue';
import HomeProcess from './Partials/HomeProcess.vue';
import HomeKBN from './Partials/HomeKBN.vue';
import HomeGovernment from './Partials/HomeGovernment.vue';
import HomeMap from './Partials/HomeMap.vue';
import HomeNews from './Partials/HomeNews.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    demografi: Object,
    layanan: Object,
    berita: Array,
    kategoriSurat: Array,
});
</script>

<template>
    <Head>
        <title>Beranda - Desa {{ $page.props.settings.nama_desa }}, {{ $page.props.settings.kabupaten }}, {{ $page.props.settings.provinsi }}</title>
        <meta name="description" :content="'Sistem Informasi Desa ' + $page.props.settings.nama_desa + ' Terpadu. Platform digital pelayanan mandiri surat keterangan warga secara online, transparan, cepat, dan terpercaya.'" />
        <meta name="keywords" :content="'Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten + ', ' + $page.props.settings.kecamatan + ', Desa Digital, AvaraDesa-' + $page.props.settings.nama_desa + ', Surat Online, Layanan Mandiri Desa'" />
        <meta property="og:title" :content="'Beranda - Desa ' + $page.props.settings.nama_desa + ', ' + $page.props.settings.kabupaten" />
        <meta property="og:description" content="Platform administrasi desa modern untuk mempermudah permohonan surat keterangan warga secara daring, transparan, dan terintegrasi penuh." />
        <meta property="og:type" content="website" />
    </Head>

    <HomeHero :demografi="demografi" />
    <HomeFeatures />
    <HomeStatistics :demografi="demografi" :layanan="layanan" />
    <HomeServices :kategori-surat="kategoriSurat" />
    <HomeProcess />
    <HomeKBN />
    <HomeGovernment />
    <HomeMap />
    <HomeNews :berita="berita" />
</template>


