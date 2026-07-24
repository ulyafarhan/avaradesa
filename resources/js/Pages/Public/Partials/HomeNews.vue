<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Newspaper, ArrowUpRight } from '@lucide/vue';
import EmptyState from '../../../Components/EmptyState.vue';
import { stripHtml } from '../../../Utils/string';

defineProps({
    berita: { type: Array, default: () => [] }
});

const imagesLoaded = ref({});
const handleImageLoad = (id) => {
    imagesLoaded.value[id] = true;
};
</script>

<template>
    <section class="bg-white py-24 md:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 border-b border-slate-200 pb-8">
                <div class="max-w-2xl border-l-4 border-amber-600 pl-6">
                    <span class="block text-xs font-bold tracking-widest text-amber-600 uppercase mb-4 font-sans">
                        Kabar Terkini
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1] font-heading">
                        Jurnal Desa.
                    </h2>
                </div>
                <div>
                    <Link href="/informasi" class="group inline-flex items-center text-sm font-bold tracking-widest text-slate-900 uppercase border-b-2 border-slate-900 pb-1 hover:text-amber-600 hover:border-amber-600 transition-colors font-sans">
                        Lihat Semua Berita <ArrowUpRight class="size-4 ml-2 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1" />
                    </Link>
                </div>
            </div>

            <div v-if="berita?.length" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                <!-- Highlighted Main News (Left Side) -->
                <article 
                    v-if="berita[0]"
                    class="lg:col-span-7 group flex flex-col"
                >
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-200 mb-8 rounded-2xl">
                        <div v-if="berita[0].cover_image && !imagesLoaded[berita[0].id]" class="absolute inset-0 animate-pulse bg-slate-300" />
                        <img 
                            v-if="berita[0].cover_image" 
                            :src="berita[0].cover_image" 
                            :alt="berita[0].judul"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            :class="imagesLoaded[berita[0].id] ? 'opacity-100' : 'opacity-0'"
                            @load="handleImageLoad(berita[0].id)"
                        />
                        <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                            <Newspaper class="size-12 mb-3 stroke-[1.5]" />
                            <span class="text-xs font-bold tracking-widest uppercase font-sans">Kabar Desa</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-500 font-sans">
                            <span class="text-amber-700 bg-amber-50 px-3 py-1 rounded">{{ berita[0].kategori }}</span>
                            <span>{{ new Date(berita[0].created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                        </div>
                        
                        <h3 class="text-3xl lg:text-4xl font-bold text-slate-900 leading-tight group-hover:text-amber-600 transition-colors duration-300 font-heading">
                            <Link :href="`/informasi/${berita[0].slug}`">{{ berita[0].judul }}</Link>
                        </h3>
                        
                        <p class="text-lg text-slate-600 line-clamp-3 leading-relaxed font-sans">
                            {{ stripHtml(berita[0].konten) }}
                        </p>
                    </div>
                </article>

                <!-- Secondary News List (Right Side) -->
                <div class="lg:col-span-5 flex flex-col gap-8 lg:gap-10 border-t lg:border-t-0 lg:border-l border-slate-200 pt-8 lg:pt-0 lg:pl-12">
                    <article 
                        v-for="item in berita.slice(1, 4)" 
                        :key="item.id"
                        class="group flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-6"
                    >
                        <div class="relative w-full sm:w-1/3 lg:w-full xl:w-1/3 aspect-[4/3] sm:aspect-square lg:aspect-[21/9] xl:aspect-square overflow-hidden bg-slate-200 rounded-xl shrink-0">
                            <div v-if="item.cover_image && !imagesLoaded[item.id]" class="absolute inset-0 animate-pulse bg-slate-300" />
                            <img 
                                v-if="item.cover_image" 
                                :src="item.cover_image" 
                                :alt="item.judul"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                :class="imagesLoaded[item.id] ? 'opacity-100' : 'opacity-0'"
                                @load="handleImageLoad(item.id)"
                            />
                            <div v-else class="absolute inset-0 flex items-center justify-center text-slate-400 bg-slate-100">
                                <Newspaper class="size-6 stroke-[1.5]" />
                            </div>
                        </div>

                        <div class="flex flex-col justify-center space-y-3 flex-1">
                            <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-slate-500 font-sans">
                                <span class="text-amber-600">{{ item.kategori }}</span>
                                <span>&middot;</span>
                                <span>{{ new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-slate-900 leading-snug group-hover:text-amber-600 transition-colors duration-300 font-heading">
                                <Link :href="`/informasi/${item.slug}`">{{ item.judul }}</Link>
                            </h3>
                        </div>
                    </article>
                </div>
            </div>
            
            <EmptyState v-else class="mt-8 border border-dashed border-slate-300 rounded-2xl bg-slate-50" title="Belum ada informasi" message="Berita desa akan tampil di sini setelah dipublikasikan." :icon="Newspaper" />
        </div>
    </section>
</template>
