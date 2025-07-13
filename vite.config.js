import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import Pusher from 'pusher-js';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        chunkSizeWarningLimit: 5000,
        minify: 'esbuild',
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'vue',
                        '@inertiajs/vue3',
                        'pinia'
                    ]
                }
            }
        }
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'panadero-solarsysinvaders': resolve(__dirname, 'vendor/panadero/panadero-solarsysinvaders/src')
        }
    },
    optimizeDeps: {
        include: ['vue', 'pinia', '@metamask/detect-provider', 'ethers']
    },
    define: {
        'import.meta.env.VITE_PUSHER_APP_KEY': JSON.stringify(process.env.PUSHER_APP_KEY),
        'import.meta.env.VITE_PUSHER_APP_CLUSTER': JSON.stringify(process.env.PUSHER_APP_CLUSTER),
    }
});
