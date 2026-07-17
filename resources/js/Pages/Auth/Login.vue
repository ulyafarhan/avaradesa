<script setup>
import { useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import AppButton from '../../Components/AppButton.vue';
import { 
    Fingerprint, 
    Lock, 
    ShieldCheck, 
    CheckCircle2, 
    User, 
    Landmark, 
    HelpCircle,
    ArrowLeft,
    AlertCircle,
    TestTube
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    testCredentials: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    nik: '',
    no_kk: '',
});

const submit = () => {
    if (form.nik.length !== 16 || form.no_kk.length !== 16) {
        return;
    }
    form.post('/login');
};

const handleNumberInput = (e, field) => {
    const val = e.target.value.replace(/\D/g, '');
    form[field] = val.slice(0, 16);
};
</script>

<template>
    <section class="min-h-[80vh] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
        <div class="w-full max-w-5xl bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden grid md:grid-cols-[1fr_1.1fr]">
            
            <div class="relative bg-gradient-to-br from-teal-800 via-teal-900 to-slate-900 text-white p-8 sm:p-12 flex flex-col justify-between overflow-hidden order-2 md:order-1">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-teal-600 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <div class="absolute -left-20 -top-20 w-60 h-60 bg-emerald-500 rounded-full blur-3xl opacity-15 pointer-events-none"></div>
                
                <div class="space-y-8 relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex size-9 items-center justify-center rounded-lg bg-teal-500 text-slate-950 shadow-md">
                                <Landmark class="size-4.5" />
                            </div>
                            <span class="text-md font-bold tracking-tight text-white">AvaraDesa Digital</span>
                        </div>
                        <a href="/" class="inline-flex items-center gap-1 text-xs text-teal-300 hover:text-white transition font-medium">
                            <ArrowLeft class="size-3.5" /> Beranda
                        </a>
                    </div>

                    <div class="space-y-3">
                        <span class="text-[10px] font-bold tracking-widest text-teal-300 uppercase bg-teal-950/60 border border-teal-800/40 px-2.5 py-1 rounded-md">Portal Warga</span>
                        <h2 class="text-3xl font-extrabold tracking-tight leading-tight">Pelayanan Mandiri Desa {{ $page.props.settings.nama_desa }}</h2>
                        <p class="text-sm text-teal-100/80 font-light leading-relaxed">
                            Selamat datang di portal pelayanan mandiri. Silakan masuk untuk mengajukan surat keterangan atau memantau progres permohonan Anda.
                        </p>
                    </div>

                    <div class="space-y-4 pt-2">
                        <div class="flex gap-3 items-start text-sm">
                            <CheckCircle2 class="size-5 text-teal-400 shrink-0 mt-0.5" />
                            <span>Ajukan berbagai jenis surat (SKU, SKTM, Domisili, dll) secara mandiri.</span>
                        </div>
                        <div class="flex gap-3 items-start text-sm">
                            <CheckCircle2 class="size-5 text-teal-400 shrink-0 mt-0.5" />
                            <span>Pantau tahapan verifikasi berkas secara real-time dari dasbor warga.</span>
                        </div>
                        <div class="flex gap-3 items-start text-sm">
                            <CheckCircle2 class="size-5 text-teal-400 shrink-0 mt-0.5" />
                            <span>Unduh berkas jadi langsung secara digital tanpa perlu antre di kantor.</span>
                        </div>
                    </div>
                </div>

                <div class="mt-12 p-4 rounded-2xl bg-white/5 border border-white/10 flex gap-3.5 items-start relative z-10">
                    <ShieldCheck class="size-6 text-teal-400 shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-bold text-white text-xs">Keamanan Data & Privasi Terjamin</h4>
                        <p class="text-[10px] text-teal-100/70 mt-0.5 leading-relaxed">
                            Data NIK dan Nomor KK Anda digunakan secara terenkripsi hanya untuk kebutuhan verifikasi kependudukan aktif desa.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 sm:p-12 flex flex-col justify-between bg-white order-1 md:order-2">
                <div class="space-y-6">
                    <div class="space-y-1">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800">Masuk Portal</h3>
                        <p class="text-xs text-slate-400">Gunakan nomor identitas kependudukan terdaftar Anda.</p>
                    </div>

                    <div 
                        v-if="form.errors.nik || form.errors.no_kk" 
                        class="p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3 text-red-700 text-xs animate-fade-in"
                    >
                        <AlertCircle class="size-4.5 shrink-0 text-red-500 mt-0.5" />
                        <div>
                            <p class="font-bold">Gagal Masuk</p>
                            <p class="mt-0.5 leading-relaxed">
                                {{ form.errors.nik || form.errors.no_kk }}
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <div class="space-y-1.5">
                            <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nomor Induk Kependudukan (NIK) <span class="text-red-600">*</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <User class="size-4" />
                                </div>
                                <input 
                                    id="nik"
                                    type="text" 
                                    :value="form.nik"
                                    @input="handleNumberInput($event, 'nik')"
                                    placeholder="Masukkan 16 digit NIK Anda" 
                                    class="block w-full pl-10 pr-4 py-3 border rounded-lg text-sm text-slate-800 bg-white placeholder:text-slate-450 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 !focus-visible:outline-none !focus-visible:ring-4 !focus-visible:ring-teal-500/10 !focus-visible:border-teal-500 !focus-visible:ring-offset-0 transition duration-150 outline-none"
                                    :class="form.nik.length > 0 && form.nik.length !== 16 ? 'border-amber-300 focus:border-amber-500 focus:ring-amber-500/10 !focus-visible:ring-amber-500/10 !focus-visible:border-amber-500' : 'border-slate-200'"
                                    required
                                    autofocus
                                />
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-semibold">
                                <span 
                                    v-if="form.nik.length > 0 && form.nik.length !== 16" 
                                    class="text-amber-600"
                                >
                                    Harus tepat 16 digit angka
                                </span>
                                <span v-else class="text-slate-400">NIK terdaftar di KK</span>
                                <span 
                                    :class="form.nik.length === 16 ? 'text-teal-600 font-bold' : 'text-slate-400'"
                                >
                                    {{ form.nik.length }}/16
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="no_kk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nomor Kartu Keluarga (KK) <span class="text-red-600">*</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <Lock class="size-4" />
                                </div>
                                <input 
                                    id="no_kk"
                                    type="password" 
                                    :value="form.no_kk"
                                    @input="handleNumberInput($event, 'no_kk')"
                                    placeholder="Masukkan 16 digit Nomor KK Anda" 
                                    class="block w-full pl-10 pr-4 py-3 border rounded-lg text-sm text-slate-800 bg-white placeholder:text-slate-450 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 !focus-visible:outline-none !focus-visible:ring-4 !focus-visible:ring-teal-500/10 !focus-visible:border-teal-500 !focus-visible:ring-offset-0 transition duration-150 outline-none"
                                    :class="form.no_kk.length > 0 && form.no_kk.length !== 16 ? 'border-amber-300 focus:border-amber-500 focus:ring-amber-500/10 !focus-visible:ring-amber-500/10 !focus-visible:border-amber-500' : 'border-slate-200'"
                                    required
                                />
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-semibold">
                                <span 
                                    v-if="form.no_kk.length > 0 && form.no_kk.length !== 16" 
                                    class="text-amber-600"
                                >
                                    Harus tepat 16 digit angka
                                </span>
                                <span v-else class="text-slate-400">Nomor KK Kepala Keluarga</span>
                                <span 
                                    :class="form.no_kk.length === 16 ? 'text-teal-600 font-bold' : 'text-slate-400'"
                                >
                                    {{ form.no_kk.length }}/16
                                </span>
                            </div>
                        </div>

                        <AppButton 
                            type="submit" 
                            :loading="form.processing" 
                            class="w-full py-3 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold transition duration-300 shadow-md shadow-teal-700/15"
                            :disabled="form.nik.length !== 16 || form.no_kk.length !== 16 || form.processing"
                        >
                            Masuk Ke Portal
                        </AppButton>
                    </form>
                    
                    <div v-if="testCredentials" class="mt-6 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 relative">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-1.5">
                                <TestTube class="size-3.5 text-emerald-700" />
                                <h4 class="text-[11px] font-bold text-emerald-800">Akses Demo Portal</h4>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-1 rounded-full bg-white text-emerald-700 border border-emerald-200">Preview</span>
                        </div>
                        <p class="text-[10px] text-emerald-700 mb-2">Gunakan data demo berikut untuk mencoba alur masuk portal warga.</p>
                        <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                            <button type="button" @click="form.nik = testCredentials.nik; form.no_kk = testCredentials.no_kk" class="text-left bg-white p-2 border border-emerald-200 rounded hover:border-emerald-400 transition cursor-pointer">
                                <span class="block text-[9px] text-emerald-600 font-sans uppercase mb-0.5">NIK Demo</span>
                                {{ testCredentials.nik }}
                            </button>
                            <button type="button" @click="form.nik = testCredentials.nik; form.no_kk = testCredentials.no_kk" class="text-left bg-white p-2 border border-emerald-200 rounded hover:border-emerald-400 transition cursor-pointer">
                                <span class="block text-[9px] text-emerald-600 font-sans uppercase mb-0.5">Nomor KK Demo</span>
                                {{ testCredentials.no_kk }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-start gap-2.5 text-[11px] text-slate-450">
                    <HelpCircle class="size-4 shrink-0 text-slate-400 mt-0.5" />
                    <p class="leading-relaxed">
                        Belum terdaftar sebagai warga digital? Silakan hubungi <strong class="font-bold text-slate-700">Operator Layanan Desa</strong> di kantor desa untuk memasukkan data KK & NIK keluarga Anda ke database.
                    </p>
                </div>
            </div>
            
        </div>
    </section>
</template>
