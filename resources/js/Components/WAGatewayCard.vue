<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Send, RefreshCw, Wifi, WifiOff, QrCode, Loader2, Smartphone } from '@lucide/vue';
import QRCode from 'qrcode-generator';

const props = defineProps({
    config: Object,
});

const status = ref({ connected: false, connection: 'disconnected', provider: 'wa-gateway' });
const qrData = ref(null);
const testForm = ref({ chat_id: '', message: '' });
const testLoading = ref(false);
const testResult = ref(null);
const loading = ref(true);

let pollTimer = null;

async function fetchStatus() {
    try {
        const res = await fetch('/admin/notifications/wa/status');
        status.value = await res.json();
    } catch {
        status.value = { connected: false, connection: 'unreachable', provider: props.config?.provider || 'wa-gateway' };
    }
}

async function fetchQr() {
    try {
        const res = await fetch('/admin/notifications/wa/qr');
        const data = await res.json();
        qrData.value = data.qr || null;
    } catch {
        qrData.value = null;
    }
}

async function sendTest() {
    if (!testForm.value.chat_id || !testForm.value.message) return;
    testLoading.value = true;
    testResult.value = null;
    try {
        const res = await fetch('/admin/notifications/wa/test', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '') },
            body: JSON.stringify(testForm.value),
        });
        const data = await res.json();
        testResult.value = data.success ? 'success' : 'error';
    } catch {
        testResult.value = 'error';
    } finally {
        testLoading.value = false;
    }
}

function renderQr(canvasEl, data) {
    if (!data || !canvasEl) return;
    canvasEl.innerHTML = '';
    const qr = QRCode(0, 'M');
    qr.addData(data);
    qr.make();
    const img = qr.createImgTag(4, 0);
    canvasEl.innerHTML = img;
}

const qrCanvas = ref(null);

watch(qrData, async (val) => {
    await nextTick();
    if (qrCanvas.value && val) renderQr(qrCanvas.value, val);
});

onMounted(async () => {
    loading.value = true;
    await Promise.all([fetchStatus(), fetchQr()]);
    loading.value = false;
    pollTimer = setInterval(async () => {
        await fetchStatus();
        if (status.value.provider === 'wa-gateway' && !status.value.connected) fetchQr();
    }, 5000);
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
});

const isFonnte = () => status.value.provider === 'fonnte';
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <component :is="isFonnte() ? Smartphone : QrCode" class="w-5 h-5 text-emerald-600" />
                <h2 class="font-semibold text-slate-900">WhatsApp {{ isFonnte() ? '(Fonnte)' : 'Gateway' }}</h2>
            </div>
            <span
                :class="status.connected ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
            >
                <span :class="status.connected ? 'bg-emerald-500' : 'bg-red-500'" class="w-1.5 h-1.5 rounded-full" />
                {{ status.connected ? 'Connected' : isFonnte() ? 'Disconnected' : status.connection === 'unreachable' ? 'Offline' : 'Disconnected' }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- QR Code (WA Gateway only) -->
            <template v-if="!isFonnte()">
                <div v-if="!status.connected" class="flex flex-col items-center gap-3">
                    <p class="text-sm text-slate-500">Scan QR code dengan WhatsApp</p>
                    <div
                        ref="qrCanvas"
                        class="bg-white p-4 rounded-lg border border-slate-200 min-h-[180px] flex items-center justify-center"
                    />
                    <div v-if="!qrData && !loading" class="text-sm text-slate-400">Menunggu QR code...</div>
                    <button
                        @click="fetchQr"
                        class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700"
                    >
                        <RefreshCw class="w-3.5 h-3.5" />
                        Refresh QR
                    </button>
                </div>
            </template>

            <!-- Fonnte Info -->
            <template v-if="isFonnte()">
                <div class="flex flex-col items-center gap-3 py-2">
                    <Smartphone class="w-10 h-10 text-slate-300" />
                    <div class="text-center">
                        <p class="text-sm text-slate-500">Terhubung via Fonnte App</p>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ config?.fonnte_token_set ? 'Token terpasang' : 'Token belum dikonfigurasi' }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Connected Info -->
            <div v-if="status.connected" class="text-center py-4">
                <Wifi class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
                <p class="text-sm font-medium text-emerald-700">WhatsApp Terhubung</p>
                <p class="text-xs text-slate-400 mt-1">Session: {{ config?.session_id }}</p>
            </div>

            <!-- Test Kirim -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Test Kirim Pesan</h3>
                <div class="space-y-3">
                    <input
                        v-model="testForm.chat_id"
                        type="text"
                        placeholder="62812xxxx (nomor HP)"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                    />
                    <textarea
                        v-model="testForm.message"
                        rows="3"
                        placeholder="Pesan test..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none"
                    />
                    <button
                        @click="sendTest"
                        :disabled="testLoading || !testForm.chat_id || !testForm.message"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <Loader2 v-if="testLoading" class="w-4 h-4 animate-spin" />
                        <Send v-else class="w-4 h-4" />
                        {{ testLoading ? 'Mengirim...' : 'Kirim Test' }}
                    </button>
                    <p v-if="testResult === 'success'" class="text-xs text-emerald-600">Terkirim!</p>
                    <p v-if="testResult === 'error'" class="text-xs text-red-600">Gagal mengirim</p>
                </div>
            </div>
        </div>
    </div>
</template>
