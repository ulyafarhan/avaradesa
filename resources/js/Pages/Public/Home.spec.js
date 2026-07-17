import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
    Head: {
        name: 'Head',
        render: () => null
    },
    Link: {
        name: 'Link',
        props: ['href'],
        template: '<a :href="href"><slot /></a>'
    }
}));

import Home from './Home.vue';

describe('Home.vue Public Page', () => {
    const defaultMountOptions = {
        props: {
            berita: [],
            pengumuman: [],
            stats: {
                total_penduduk: 1500,
                total_keluarga: 450,
                pengajuan_selesai: 120
            }
        },
        global: {
            mocks: {
                $page: {
                    props: {
                        settings: {
                            banner_desa: null,
                            nama_desa: 'Udeung',
                            kecamatan: 'Bandar Baru',
                            kabupaten: 'Pidie Jaya',
                            provinsi: 'Aceh'
                        }
                    }
                }
            },
            stubs: {
                Link: true,
                Head: true,
                ArrowUpRight: true,
                ArrowRight: true,
            }
        }
    };

    it('renders the village portal header', () => {
        const wrapper = mount(Home, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Desa Udeung');
        expect(wrapper.text()).toContain('Sistem Layanan Mandiri Digital');
    });

    it('displays empty news fallback', () => {
        const wrapper = mount(Home, defaultMountOptions);
        expect(wrapper.text()).toContain('Belum ada informasi');
    });
});
