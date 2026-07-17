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

import Statistik from './Statistik.vue';

describe('Statistik.vue Page', () => {
    const defaultMountOptions = {
        props: {
            demografi: {
                total: 1000,
                gender: { laki_laki: 510, perempuan: 490 },
                usia: { balita: 100, anak: 200, remaja: 150, produktif: 450, lansia: 100 },
                pendidikan: { 'SD': 300, 'SMP': 300, 'SMA': 300, 'PT': 100 },
                pekerjaan: { 'Tani': 400, 'PNS': 100, 'Wiraswasta': 200, 'Lainnya': 300 },
                agama: { 'Islam': 1000, 'Lainnya': 0 }
            }
        },
        global: {
            mocks: {
                $page: {
                    props: {
                        settings: {
                            nama_desa: 'Udeung',
                        }
                    }
                }
            },
            stubs: {
                Link: true,
                Head: true,
            }
        }
    };

    it('renders statistical details', () => {
        const wrapper = mount(Statistik, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Statistik Desa Udeung');
        expect(wrapper.text()).toContain('Profil Demografi');
    });

    it('allows switching tabs between demografi categories', async () => {
        const wrapper = mount(Statistik, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Pekerjaan');
        expect(wrapper.text()).toContain('Pendidikan');
        
        const tabButtons = wrapper.findAll('button');
        const pekerjaanTab = tabButtons.find(b => b.text().includes('Pekerjaan'));
        
        if (pekerjaanTab) {
            await pekerjaanTab.trigger('click');
            expect(pekerjaanTab.classes()).toContain('active');
        }
    });
});
