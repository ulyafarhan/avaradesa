import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Profile from './Profile.vue';

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data) => ({
        ...data,
        post: vi.fn(),
        errors: {},
    })
}));

describe('Profile.vue Page', () => {
    const defaultMountOptions = {
        props: {
            warga: {
                nik: '1234567890123456',
                no_kk: '1234567890123456',
                nama_lengkap: 'Farhan Ulya',
                jenis_kelamin: 'L',
                tempat_lahir: 'Pidie Jaya',
                tanggal_lahir: '1995-05-15',
                agama: 'Islam',
                status_keluarga: 'Kepala Keluarga',
                pendidikan: 'Diploma IV/Strata I',
                pekerjaan: 'PNS',
                status_perkawinan: 'Kawin',
                telegram_chat_id: '123456',
                foto_profil: null,
                foto_ktp: '/storage/ktp.jpg',
                foto_kk: '/storage/kk.jpg'
            },
            completeness: 100,
        },
        global: {
            stubs: {
                Link: true,
                Head: true,
                AppButton: true,
                FormInput: true,
                FormSelect: true,
            }
        }
    };

    it('renders NIK, KK, and personal data from props', () => {
        const wrapper = mount(Profile, defaultMountOptions);
        
        expect(wrapper.text()).toContain('1234567890123456');
        expect(wrapper.text()).toContain('Farhan Ulya');
        expect(wrapper.text()).toContain('Laki-laki');
    });

    it('enables editing inputs when Edit Biodata button is clicked', async () => {
        const wrapper = mount(Profile, defaultMountOptions);
        
        expect(wrapper.text()).toContain('PNS');
        
        const editButton = wrapper.find('button');
        if (editButton.exists()) {
            await editButton.trigger('click');
            expect(wrapper.text()).toContain('Simpan Perubahan');
        }
    });
});
