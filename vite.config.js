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
    server: {
        host: process.env.VITE_SERVER_HOST || '192.168.2.20', // Use your network IP
        port: parseInt(process.env.VITE_SERVER_PORT || '5173'),
        cors: true,
        hmr: {
            host: process.env.VITE_SERVER_HOST || '192.168.2.20', // Use your network IP
            port: parseInt(process.env.VITE_SERVER_PORT || '5173'),
        },
    },
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
            'panadero-solarsysinvaders': resolve(__dirname, 'vendor/panadero/panadero-solarsysinvaders/client/src/index.js')
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
