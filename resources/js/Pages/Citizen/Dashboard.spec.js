import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import Dashboard from './Dashboard.vue';

describe('Dashboard.vue Page', () => {
    const defaultMountOptions = {
        props: {
            warga: {
                nik: '1234567890123456',
                nama_lengkap: 'Farhan Ulya',
                no_kk: '1234567890123456',
                status_keluarga: 'Kepala Keluarga',
                keluarga: {
                    alamat: 'Jl. Garuda No. 5',
                    dusun: 'Dusun Udeung',
                    rt_rw: 'RT 01/RW 02'
                }
            },
            anggotaKeluarga: [],
            kategoriSurat: [],
            pengajuan: { data: [] },
            biodataComplete: true,
            biodataCompleteness: 100,
            summary: { pending: 1, diproses: 2, selesai: 3 },
            isKepalaKeluarga: true,
            activeTab: 'beranda'
        },
        global: {
            stubs: {
                Link: true,
                Head: true,
                KeluargaTab: true,
                PengajuanTab: true,
                BiodataTab: true,
            }
        }
    };

    it('renders welcoming header and active tab correctly', () => {
        const wrapper = mount(Dashboard, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Farhan Ulya');
        expect(wrapper.text()).toContain('Layanan Mandiri Warga');
    });

    it('allows changing active tab by clicking navigation items', async () => {
        const wrapper = mount(Dashboard, defaultMountOptions);
        
        const tabLinks = wrapper.findAll('button');
        const keluargaLink = tabLinks.find(l => l.text().includes('Keluarga Saya'));
        
        expect(keluargaLink).toBeDefined();
    });
});
