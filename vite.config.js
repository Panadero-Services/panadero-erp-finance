import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        chunkSizeWarningLimit: 1000, // Increase the size limit to 1000kb
        rollupOptions: {
            output: {
                manualChunks: {
                    'panadero-games': [
                        'resources/js/panadero/panadero-minigames/othello.vue',
                        'resources/js/panadero/panadero-minigames/breakout.vue'
                    ],
                    'vendor': [
                        'vue',
                        // Add other major dependencies here
                    ]
                }
            }
        }
    },
    // Reduce console noise
    logLevel: 'warn',
    clearScreen: false,
    server: {
        hmr: {
            overlay: false,
        },
    },
});
