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
        host: 'localhost', // Use localhost instead of 127.0.0.1
        port: parseInt(process.env.VITE_SERVER_PORT),
        cors: {
            origin: true, // Allow all origins for development
            credentials: true
        },
        hmr: {
            host: 'localhost', // Use localhost instead of 127.0.0.1
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
            'panadero-erp-compliance': resolve(__dirname, 'packages/panadero-erp-compliance'),
        }
    },
    optimizeDeps: {
        include: ['vue', 'pinia', '@metamask/detect-provider', 'ethers', 'panadero-erp-compliance', 'panadero-shared-components', 'panadero-shared-styling']
    },
    define: {
        'import.meta.env.VITE_PUSHER_APP_KEY': JSON.stringify(process.env.PUSHER_APP_KEY),
        'import.meta.env.VITE_PUSHER_APP_CLUSTER': JSON.stringify(process.env.PUSHER_APP_CLUSTER),
    }
});
