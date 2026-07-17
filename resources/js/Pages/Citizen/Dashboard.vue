<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import CitizenLayout from '../../Layouts/CitizenLayout.vue';
import BerandaTab from './Tabs/BerandaTab.vue';
import PengajuanTab from './Tabs/PengajuanTab.vue';
import KeluargaTab from './Tabs/KeluargaTab.vue';
import BiodataTab from './Tabs/BiodataTab.vue';

defineOptions({ layout: CitizenLayout });

const props = defineProps({
    warga: Object,
    kategoriSurat: Array,
    pengajuan: Object,
    summary: Object,
    biodataComplete: Boolean,
    biodataCompleteness: Number,
    isKepalaKeluarga: Boolean,
    anggotaKeluarga: Array,
});

const activeTab = ref('beranda');
const tabs = [
    { id: 'beranda', label: 'Beranda', icon: 'home' },
    { id: 'pengajuan', label: 'Pengajuan Surat', icon: 'file' },
];

const setActiveTab = (tabId) => {
    activeTab.value = tabId;
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url.toString());
    } catch (e) {}
};

const syncTabWithUrl = () => {
    try {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab && ['beranda', 'pengajuan', 'keluarga', 'biodata'].includes(tab)) {
            activeTab.value = tab;
        }
    } catch (e) {}
};

let pollingTimeout = null;

const startPolling = () => {
    const delay = Math.floor(Math.random() * (45000 - 30000 + 1)) + 30000;
    pollingTimeout = setTimeout(() => {
        router.reload({
            only: ['pengajuan', 'summary'],
            onFinish: () => {
                startPolling();
            }
        });
    }, delay);
};

onMounted(() => {
    syncTabWithUrl();
    startPolling();
});

onUnmounted(() => {
    if (pollingTimeout) {
        clearTimeout(pollingTimeout);
    }
});

const page = usePage();
watch(() => page.url, () => {
    syncTabWithUrl();
});

const recentPengajuan = computed(() => (props.pengajuan?.data || []).slice(0, 3));
</script>

<template>
    <div class="google-editorial min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto space-y-8">


            <BerandaTab
                v-if="activeTab === 'beranda'"
                :warga="warga"
                :summary="summary"
                :recent-pengajuan="recentPengajuan"
                :biodata-complete="biodataComplete"
                :biodata-completeness="biodataCompleteness"
                @set-active-tab="setActiveTab"
            />

            <PengajuanTab
                v-else-if="activeTab === 'pengajuan'"
                :kategori-surat="kategoriSurat"
                :pengajuan="pengajuan"
                :biodata-complete="biodataComplete"
                :is-kepala-keluarga="isKepalaKeluarga"
                @set-active-tab="setActiveTab"
            />

            <KeluargaTab
                v-else-if="activeTab === 'keluarga'"
                :warga="warga"
                :is-kepala-keluarga="isKepalaKeluarga"
                :anggota-keluarga="anggotaKeluarga"
                :biodata-complete="biodataComplete"
                :kategori-surat="kategoriSurat"
            />

            <BiodataTab
                v-else-if="activeTab === 'biodata'"
                :warga="warga"
                :biodata-complete="biodataComplete"
                :biodata-completeness="biodataCompleteness"
            />
        </div>
    </div>
</template>

<style>


.google-editorial {
    font-family: inherit;
    color: #202124;
    background-color: #F8F9FA;
}

.headline-lg {
    font-size: 32px;
    font-weight: 400;
    line-height: 40px;
    letter-spacing: 0px;
    color: #202124;
}

.headline-md {
    font-size: 22px;
    font-weight: 400;
    line-height: 30px;
    letter-spacing: 0px;
    color: #202124;
}

.headline-sm {
    font-size: 20px;
    font-weight: 400;
    line-height: 28px;
    letter-spacing: 0px;
    color: #202124;
}

.body-lg {
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    letter-spacing: 0.25px;
}

.body-md {
    font-size: 14px;
    font-weight: 400;
    line-height: 22px;
    letter-spacing: 0.15px;
}

.body-sm {
    font-size: 12px;
    font-weight: 400;
    line-height: 18px;
    letter-spacing: 0.2px;
}

.label-lg {
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
    letter-spacing: 0px;
}

.label-md {
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    letter-spacing: 0px;
}

.label-sm {
    font-size: 12px;
    font-weight: 500;
    line-height: 16px;
    letter-spacing: 0.04em;
}

.overline-label {
    font-size: 11px;
    font-weight: 500;
    line-height: 16px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #5F6368;
}

.text-primary { color: #1A73E8; }
.text-secondary { color: #5F6368; }
.text-neutral { color: #202124; }
.text-error { color: #D93025; }
.text-warning-dark { color: #B06000; }

.bg-primary { background-color: #1A73E8; }
.bg-warning { background-color: #F9AB00; }

.editorial-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,0.1), 0 1px 3px 1px rgba(60,64,67,0.05);
}

.welcome-card {
}

.stat-card {
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    cursor: pointer;
    transition: all 0.2s;
}
.action-card:hover {
    background-color: #F8F9FA;
    border-color: #D1D5DB;
}

.list-item-card {
    padding: 20px;
    transition: all 0.2s;
}
.list-item-card:hover {
    border-color: #D1D5DB;
    box-shadow: 0 4px 12px 0 rgba(60,64,67,0.08);
}

.service-card {
    padding: 24px;
}

.member-card {
    padding: 20px;
}

.biodata-field-card {
    padding: 16px 20px;
}

.avatar-circle-lg {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 9999px;
    background-color: #E8F0FE;
    color: #1A73E8;
    font-weight: 600;
    font-size: 22px;
}

.avatar-circle-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 9999px;
}

.alert-box {
    display: flex;
    gap: 14px;
    padding: 16px;
    border-radius: 12px;
    align-items: center;
}

.warning-alert {
    background-color: #FEF7E0;
    border: 1px solid #FEEFC3;
    color: #B06000;
}

.error-alert {
    background-color: #FCE8E6;
    border: 1px solid #FAD2CF;
    color: #D93025;
}

.icon-wrapper, .action-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 9999px;
}

.bg-warning-soft { background-color: #FEF7E0; color: #B06000; }
.bg-primary-soft { background-color: #E8F0FE; color: #1A73E8; }
.bg-success-soft { background-color: #E6F4EA; color: #137333; }
.bg-accent-soft { background-color: #D2E3FC; color: #1A73E8; }

.btn-primary {
    background-color: #1A73E8;
    color: #FFFFFF;
    font-size: 14px;
    font-weight: 500;
    border-radius: 9999px;
    padding: 11px 24px;
    height: 48px;
    transition: background-color 0.2s;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-primary:hover {
    background-color: #1557B0;
}

.btn-secondary {
    background-color: #FFFFFF;
    color: #1A73E8;
    font-size: 14px;
    font-weight: 500;
    border-radius: 9999px;
    padding: 11px 24px;
    height: 48px;
    border: 1px solid #D2E3FC;
    transition: background-color 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-secondary:hover {
    background-color: #F4F8FF;
}

.success-label {
    font-size: 12px;
    font-weight: 600;
    color: #137333;
}

.badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.tertiary-badge {
    background-color: #E8F0FE;
    color: #1A73E8;
}

.success-badge {
    background-color: #E6F4EA;
    color: #137333;
}

.error-badge {
    background-color: #FCE8E6;
    color: #C5221F;
}

.success-chip {
    background-color: #E6F4EA;
    color: #137333;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 9999px;
}
</style>
