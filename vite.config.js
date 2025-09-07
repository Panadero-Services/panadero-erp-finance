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
        host: process.env.VITE_SERVER_HOST, // Use your network IP
        port: parseInt(process.env.VITE_SERVER_PORT),
        cors: true,
        hmr: {
            host: process.env.VITE_SERVER_HOST, // Use your network IP
            port: parseInt(process.env.VITE_SERVER_PORT),
        },
        proxy: {
            '/api/openai': {
                target: 'https://api.openai.com',
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api\/openai/, '/v1')
            },
            '/api/claude': {
                target: 'https://api.anthropic.com',
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api\/claude/, '/v1')
            }
        }
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
            'panadero-solarsysinvaders': resolve(__dirname, 'vendor/panadero/panadero-solarsysinvaders/client/src/index.js'),
            'panadero-erp-finance': resolve(__dirname, 'packages/panadero-erp-finance'),
            'panadero-erp-inventory': resolve(__dirname, 'packages/panadero-erp-inventory'),
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
