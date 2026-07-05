import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [vue()],
    test: {
        globals: true,
        environment: 'jsdom',
        include: ['resources/js/**/*.spec.js', 'resources/js/**/*.test.js'],
        exclude: ['node_modules', 'vendor'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'json', 'html'],
            include: ['resources/js/**/*.{js,vue}'],
            exclude: [
                'resources/js/**/*.spec.js',
                'resources/js/**/*.test.js',
            ],
        },
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
});
