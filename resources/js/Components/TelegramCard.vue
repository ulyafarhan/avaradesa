<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Send, RefreshCw, Radio, Loader2, Users, MessageSquare } from '@lucide/vue';

const props = defineProps({
    botInfo: Object,
    targetCount: Number,
});

const status = ref({ connected: false });
const broadcastForm = ref({ target: 'all', message: '' });
const broadcastLoading = ref(false);
const broadcastResult = ref(null);

const charCount = computed(() => broadcastForm.value.message.length);
const maxChars = 4096;

async function fetchStatus() {
    try {
        const res = await fetch('/admin/notifications/telegram/status');
        status.value = await res.json();
    } catch {
        status.value = { connected: false };
    }
}

async function sendBroadcast() {
    if (!broadcastForm.value.message || broadcastLoading.value) return;
    broadcastLoading.value = true;
    broadcastResult.value = null;

    try {
        const res = await fetch('/admin/notifications/telegram/broadcast', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '') },
            body: JSON.stringify({
                target: broadcastForm.value.target,
                message: broadcastForm.value.message,
                ...(broadcastForm.value.target === 'custom' ? { chat_ids: broadcastForm.value.customIds?.split('\n').filter(Boolean) } : {}),
            }),
        });
        const data = await res.json();
        broadcastResult.value = data;
    } catch {
        broadcastResult.value = { success: 0, failed: 0, error: true };
    } finally {
        broadcastLoading.value = false;
    }
}

let pollTimer = null;

onMounted(async () => {
    await fetchStatus();
    pollTimer = setInterval(fetchStatus, 10000);
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
});
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <Radio class="w-5 h-5 text-blue-600" />
                <h2 class="font-semibold text-slate-900">Telegram Bot</h2>
            </div>
            <span
                :class="status.connected ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
            >
                <span :class="status.connected ? 'bg-emerald-500' : 'bg-red-500'" class="w-1.5 h-1.5 rounded-full" />
                {{ status.connected ? 'Connected' : 'Offline' }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- Bot Info -->
            <div v-if="status.connected" class="bg-slate-50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-slate-500">Bot Name:</span>
                        <span class="font-medium text-slate-900 ml-1">{{ status.bot_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Username:</span>
                        <span class="font-medium text-slate-900 ml-1">@{{ status.bot_username }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Target:</span>
                        <span class="font-medium text-slate-900 ml-1">{{ targetCount }} warga terdaftar</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Token:</span>
                        <span class="font-medium text-slate-900 ml-1">{{ botInfo?.token_set ? 'Tersimpan' : 'Belum diatur' }}</span>
                    </div>
                </div>
            </div>

            <!-- Offline Warning -->
            <div v-if="!status.connected && botInfo?.token_set" class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700">
                Bot token tersedia tapi koneksi gagal. Periksa token di <code class="bg-amber-100 px-1 rounded">.env</code>
            </div>

            <!-- Broadcast -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Broadcast Pesan</h3>

                <div class="space-y-3">
                    <!-- Target -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Target</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                <input v-model="broadcastForm.target" type="radio" value="all" class="text-blue-600 focus:ring-blue-500" />
                                <Users class="w-4 h-4 text-slate-400" />
                                Semua Warga
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                <input v-model="broadcastForm.target" type="radio" value="custom" class="text-blue-600 focus:ring-blue-500" />
                                <MessageSquare class="w-4 h-4 text-slate-400" />
                                Custom
                            </label>
                        </div>
                    </div>

                    <!-- Custom Chat IDs -->
                    <div v-if="broadcastForm.target === 'custom'">
                        <textarea
                            v-model="broadcastForm.customIds"
                            rows="3"
                            placeholder="Masukkan Chat ID, satu per baris:&#10;-1001234567890&#10;-1009876543210"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none font-mono"
                        />
                        <p class="text-xs text-slate-400 mt-1">Chat ID Telegram, satu per baris</p>
                    </div>

                    <!-- Message -->
                    <div>
                        <textarea
                            v-model="broadcastForm.message"
                            rows="5"
                            placeholder="Pesan broadcast..."
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"
                        />
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-slate-400">
                                {{ broadcastForm.target === 'all' ? `${targetCount} penerima` : 'Custom target' }}
                            </p>
                            <p
                                :class="charCount > maxChars ? 'text-red-500 font-medium' : 'text-slate-400'"
                                class="text-xs"
                            >
                                {{ charCount }}/{{ maxChars }}
                            </p>
                        </div>
                    </div>

                    <!-- Send Button -->
                    <button
                        @click="sendBroadcast"
                        :disabled="broadcastLoading || !broadcastForm.message || charCount > maxChars || (broadcastForm.target === 'custom' && !broadcastForm.customIds)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <Loader2 v-if="broadcastLoading" class="w-4 h-4 animate-spin" />
                        <Send v-else class="w-4 h-4" />
                        {{ broadcastLoading ? 'Mengirim...' : 'Kirim Broadcast' }}
                    </button>

                    <!-- Result -->
                    <div v-if="broadcastResult && !broadcastResult.error" class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-sm">
                        <p class="text-emerald-700 font-medium">Broadcast selesai</p>
                        <p class="text-emerald-600 text-xs mt-1">
                            Berhasil: {{ broadcastResult.success }} | Gagal: {{ broadcastResult.failed }} | Total: {{ broadcastResult.total }}
                        </p>
                    </div>
                    <div v-if="broadcastResult?.error" class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                        Gagal mengirim broadcast
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
