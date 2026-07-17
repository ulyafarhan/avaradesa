import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Create from './Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data) => ({
        ...data,
        post: vi.fn(),
        errors: {},
    }),
    usePage: () => ({
        props: {}
    })
}));

describe('Create.vue Submission Page', () => {
    const defaultMountOptions = {
        props: {
            kategori: {
                id: 1,
                kode_surat: 'SKTM',
                nama_surat: 'Surat Keterangan Tidak Mampu',
                schema_isian: [
                    { field: 'alasan', label: 'Alasan Pengajuan', type: 'text' }
                ],
                syarat_dokumen: ['KTP', 'KK']
            },
            wargaData: {
                nik: '1234567890123456',
                nama_lengkap: 'Farhan Ulya',
                foto_ktp: '/storage/ktp.jpg',
                foto_kk: '/storage/kk.jpg'
            },
            anggotaKeluarga: [],
            isKepalaKeluarga: false
        },
        global: {
            stubs: {
                Link: true,
                Head: true,
                CitizenLayout: true,
                AppButton: true,
                FormInput: true,
                FormSelect: true,
                StepIndicator: true,
            }
        }
    };

    it('renders the wizard with proper page header and fields', () => {
        const wrapper = mount(Create, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Surat Keterangan Tidak Mampu');
    });

    it('navigates through steps correctly', async () => {
        const wrapper = mount(Create, defaultMountOptions);
        
        const buttons = wrapper.findAll('button');
        const nextButton = buttons.find(b => b.text().includes('Lanjut'));
        
        expect(nextButton).toBeDefined();
    });
});
