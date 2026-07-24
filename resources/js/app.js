import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { createApp, h, ref } from 'vue';

const progress = ref(false);

router.on('start', () => {
    progress.value = true;
});

router.on('finish', () => {
    progress.value = false;
});

createInertiaApp({
    title: (title) => (title ? `${title} - AvaraDesa` : 'AvaraDesa'),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        createApp({
            setup() {
                return () => h(App, props);
            },
        })
            .use(plugin)
            .provide('pageProgress', progress)
            .mount(el);
    },
});
