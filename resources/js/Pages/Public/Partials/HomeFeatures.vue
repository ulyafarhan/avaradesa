<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { 
    Users, 
    FileText, 
    Newspaper, 
    PhoneCall, 
    ArrowRight,
    ArrowUpRight
} from '@lucide/vue';

const page = usePage();
const sebutanWilayah = page.props.settings.sebutan_desa || 'Pemerintah';

const featureItems = [
    {
        title: "Layanan Mandiri",
        desc: "Warga dapat mengajukan berbagai jenis surat keterangan secara mandiri online dengan cepat tanpa antre.",
        icon: FileText,
        href: "/login",
        class: "md:col-span-2 md:row-span-2 bg-slate-950 text-white shadow-xl shadow-slate-900/10", // Dark premium card
        titleClass: "text-white",
        descClass: "text-slate-400",
        iconWrapperClass: "bg-white/10 text-white",
        hoverIconClass: "group-hover:translate-x-1 group-hover:-translate-y-1 text-slate-400 group-hover:text-amber-500"
    },
    {
        title: "Transparansi Data",
        desc: `Statistik demografi ${sebutanWilayah} secara real-time.`,
        icon: Users,
        href: "/statistik",
        class: "md:col-span-1 md:row-span-1 bg-white border border-slate-200",
        titleClass: "text-slate-900",
        descClass: "text-slate-500",
        iconWrapperClass: "bg-slate-50 text-slate-700",
        hoverIconClass: "group-hover:translate-x-1 group-hover:-translate-y-1 text-slate-400 group-hover:text-amber-600"
    },
    {
        title: "Kabar Informasi",
        desc: `Pengumuman & berita terbaru seputar ${sebutanWilayah}.`,
        icon: Newspaper,
        href: "/informasi",
        class: "md:col-span-1 md:row-span-1 bg-slate-50 border border-slate-200",
        titleClass: "text-slate-900",
        descClass: "text-slate-500",
        iconWrapperClass: "bg-white text-slate-700 shadow-sm",
        hoverIconClass: "group-hover:translate-x-1 group-hover:-translate-y-1 text-slate-400 group-hover:text-amber-600"
    },
    {
        title: "Dukungan Cepat",
        desc: "Hubungi operator pelayanan langsung melalui WhatsApp untuk bantuan dokumen.",
        icon: PhoneCall,
        href: "https://wa.me/" + (page.props.settings.telepon_operator ? page.props.settings.telepon_operator.replace(/\D/g, '') : '6281234567890'),
        external: true,
        class: "md:col-span-2 md:row-span-1 bg-amber-50 border border-amber-200",
        titleClass: "text-amber-900",
        descClass: "text-amber-700",
        iconWrapperClass: "bg-amber-100 text-amber-700",
        hoverIconClass: "group-hover:translate-x-1 group-hover:-translate-y-1 text-amber-600 group-hover:text-amber-800"
    }
];
</script>

<template>
    <section class="py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Minimalist Header -->
            <div class="mb-16 max-w-2xl border-l-4 border-amber-600 pl-6">
                <h2 class="text-4xl font-bold text-slate-900 tracking-tight font-heading">Fasilitas Layanan</h2>
                <p class="mt-4 text-lg text-slate-500 font-sans">Akses cepat menuju layanan mandiri dan informasi publik yang kami sediakan untuk kenyamanan warga.</p>
            </div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-4 lg:gap-6 auto-rows-fr">
                <template v-for="(item, index) in featureItems" :key="item.title">
                    
                    <a 
                        v-if="item.external"
                        :href="item.href"
                        target="_blank"
                        :class="[
                            'group flex flex-col justify-between p-8 sm:p-10 rounded-[2rem] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-amber-200 relative overflow-hidden',
                            item.class
                        ]"
                    >
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-8">
                                <div :class="['size-14 rounded-2xl flex items-center justify-center transition-colors duration-300', item.iconWrapperClass]">
                                    <component :is="item.icon" class="size-6" />
                                </div>
                                <ArrowUpRight :class="['size-6 transition-all duration-300', item.hoverIconClass]" />
                            </div>
                            <div class="mt-auto">
                                <h3 :class="['text-xl md:text-2xl font-bold mb-3 font-heading', item.titleClass]">
                                    {{ item.title }}
                                </h3>
                                <p :class="['text-base leading-relaxed font-sans', item.descClass]">
                                    {{ item.desc }}
                                </p>
                            </div>
                        </div>
                    </a>
                    
                    <Link 
                        v-else
                        :href="item.href"
                        :class="[
                            'group flex flex-col justify-between p-8 sm:p-10 rounded-[2rem] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-slate-200 relative overflow-hidden hover:-translate-y-1',
                            item.class
                        ]"
                    >
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-8">
                                <div :class="['size-14 rounded-2xl flex items-center justify-center transition-colors duration-300', item.iconWrapperClass]">
                                    <component :is="item.icon" class="size-6" />
                                </div>
                                <ArrowRight :class="['size-6 transition-all duration-300', item.hoverIconClass]" />
                            </div>
                            <div class="mt-auto">
                                <h3 :class="['text-xl font-bold mb-3 font-heading group-hover:text-amber-500 transition-colors', index === 0 ? 'md:text-4xl lg:text-5xl mb-6' : 'md:text-2xl', item.titleClass]">
                                    {{ item.title }}
                                </h3>
                                <p :class="['text-base leading-relaxed font-sans', index === 0 ? 'md:text-lg max-w-sm' : '', item.descClass]">
                                    {{ item.desc }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </template>
            </div>
        </div>
    </section>
</template>
