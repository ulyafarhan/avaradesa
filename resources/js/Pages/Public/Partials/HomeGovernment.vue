<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { settings } = usePage().props;

const aparaturList = computed(() => [
    {
        name: settings.nama_kepala_desa || 'Kepala Desa',
        role: 'Kepala Desa',
        photo: settings.foto_kepala_desa || 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=600&auto=format&fit=crop'
    },
    {
        name: settings.nama_sekdes || 'Sekretaris Desa',
        role: 'Sekretaris Desa',
        photo: settings.foto_sekdes || 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=600&auto=format&fit=crop'
    },
    {
        name: settings.nama_operator || 'Operator Layanan',
        role: 'Operator Layanan',
        photo: settings.foto_operator || 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=600&auto=format&fit=crop'
    },
]);
</script>

<template>
    <section class="bg-white py-24 md:py-32 border-t border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 md:mb-24">
                <div class="max-w-2xl border-l-4 border-amber-600 pl-6">
                    <span class="block text-xs font-bold tracking-widest text-amber-600 uppercase mb-4 font-sans">
                        Struktur Pemerintahan
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-[1.1] font-heading">
                        Aparatur Desa.
                    </h2>
                </div>
                <div class="max-w-md">
                    <p class="text-slate-500 text-lg leading-relaxed font-sans">
                        Jajaran aparatur desa {{ settings.nama_desa }} yang berdedikasi melayani kepentingan administrasi publik dan pembangunan yang transparan.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-16">
                <div
                    v-for="(aparat, index) in aparaturList"
                    :key="aparat.name"
                    class="group relative"
                >
                    <div class="aspect-[3/4] w-full overflow-hidden bg-slate-100 rounded-lg mb-6 relative">
                        <img
                            :src="aparat.photo"
                            :alt="aparat.name"
                            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-700"></div>
                    </div>
                    
                    <div class="flex flex-col border-t border-slate-200 pt-5">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 font-sans">{{ aparat.role }}</span>
                        <h3 class="text-2xl font-bold text-slate-900 font-heading group-hover:text-amber-600 transition-colors">{{ aparat.name }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
