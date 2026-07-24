<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle, X } from '@lucide/vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const visible = ref({ success: false, error: false });
const customToast = ref({ success: '', error: '' });

const displaySuccess = computed(() => customToast.value.success || flash.value.success);
const displayError = computed(() => customToast.value.error || flash.value.error);

watch(() => flash.value.success, (val) => {
    if (val) {
        visible.value.success = true;
        setTimeout(() => { visible.value.success = false; }, 4000);
    }
}, { immediate: true });

watch(() => flash.value.error, (val) => {
    if (val) {
        visible.value.error = true;
        setTimeout(() => { visible.value.error = false; }, 4000);
    }
}, { immediate: true });

const handleToast = (e) => {
    if (e.detail.type === 'success') {
        customToast.value.success = e.detail.message;
        visible.value.success = true;
        setTimeout(() => { visible.value.success = false; customToast.value.success = ''; }, 4000);
    } else {
        customToast.value.error = e.detail.message;
        visible.value.error = true;
        setTimeout(() => { visible.value.error = false; customToast.value.error = ''; }, 4000);
    }
};

onMounted(() => {
    window.addEventListener('toast', handleToast);
});

onUnmounted(() => {
    window.removeEventListener('toast', handleToast);
});
</script>

<template>
    <div class="fixed right-4 top-4 z-[9999] space-y-2">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
        >
            <div v-if="visible.success && displaySuccess" class="flex items-center gap-3 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-lg" @click="visible.success = false">
                <CheckCircle class="size-5 shrink-0" />
                <span>{{ displaySuccess }}</span>
                <button class="ml-2 shrink-0 rounded-full p-0.5 hover:bg-white/20" @click.stop="visible.success = false"><X class="size-4" /></button>
            </div>
        </Transition>
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
        >
            <div v-if="visible.error && displayError" class="flex items-center gap-3 rounded-lg bg-red-600 px-4 py-3 text-sm font-medium text-white shadow-lg" @click="visible.error = false">
                <XCircle class="size-5 shrink-0" />
                <span>{{ displayError }}</span>
                <button class="ml-2 shrink-0 rounded-full p-0.5 hover:bg-white/20" @click.stop="visible.error = false"><X class="size-4" /></button>
            </div>
        </Transition>
    </div>
</template>
