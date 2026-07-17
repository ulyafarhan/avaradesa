import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import Login from './Login.vue';

describe('Login.vue Page', () => {
    const defaultMountOptions = {
        global: {
            stubs: {
                Link: true,
                Head: true,
            }
        }
    };

    it('renders the login page header and options correctly', () => {
        const wrapper = mount(Login, defaultMountOptions);
        
        expect(wrapper.text()).toContain('Masuk Portal');
        expect(wrapper.text()).toContain('Pelayanan Mandiri Desa Udeung');
        expect(wrapper.text()).toContain('Portal Warga');
    });

    it('shows warga login form by default with NIK and KK inputs', () => {
        const wrapper = mount(Login, defaultMountOptions);
        
        expect(wrapper.find('input[id="nik"]').exists()).toBe(true);
        expect(wrapper.find('input[id="no_kk"]').exists()).toBe(true);
    });
});
