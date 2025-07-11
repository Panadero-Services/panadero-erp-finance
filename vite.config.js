import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

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
        chunkSizeWarningLimit: 3000, // Increase limit temporarily while we optimize
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Core framework chunks
                    if (id.includes('node_modules/vue')) return 'core-vue';
                    if (id.includes('@inertiajs')) return 'core-inertia';
                    if (id.includes('pinia')) return 'core-pinia';
                    if (id.includes('@vueuse')) return 'core-vueuse';
                    
                    // Feature areas based on routes
                    if (id.includes('/pages/home/')) return 'page-home';
                    if (id.includes('/pages/admin/')) return 'page-admin';
                    if (id.includes('/pages/indigo1/')) return 'page-indigo';
                    if (id.includes('/pages/project/')) return 'page-project';
                    if (id.includes('/pages/content/')) return 'page-content';
                    if (id.includes('/pages/Auth/')) return 'page-auth';
                    
                    // Games - split individually
                    if (id.includes('panadero-minigames/othello')) return 'game-othello';
                    if (id.includes('panadero-minigames/breakout')) return 'game-breakout';
                    
                    // UI Components by type
                    if (id.includes('/components/buttons/')) return 'ui-buttons';
                    if (id.includes('/components/cards/')) return 'ui-cards';
                    if (id.includes('/components/modals/')) return 'ui-modals';
                    if (id.includes('/components/layouts/')) return 'ui-layouts';
                    if (id.includes('/components/inputs/')) return 'ui-inputs';
                    if (id.includes('/components/icons/')) return 'ui-icons';
                    
                    // Stores by feature
                    if (id.includes('/stores/session')) return 'store-session';
                    if (id.includes('/stores/user')) return 'store-user';
                    if (id.includes('/stores/')) return 'store-other';
                    
                    // Utils and helpers
                    if (id.includes('/utils/')) return 'utils';
                    if (id.includes('/helpers/')) return 'helpers';
                    
                    // Third-party dependencies by category
                    if (id.includes('node_modules/lodash')) return 'vendor-utils';
                    if (id.includes('node_modules/axios')) return 'vendor-http';
                    if (id.includes('node_modules/@headlessui')) return 'vendor-ui';
                    if (id.includes('node_modules/@heroicons')) return 'vendor-icons';
                    
                    // Remaining node_modules split by first letter to avoid large chunks
                    if (id.includes('node_modules/')) {
                        const moduleNameMatch = id.match(/node_modules\/([^/]+)/);
                        if (moduleNameMatch) {
                            const firstChar = moduleNameMatch[1].charAt(0).toLowerCase();
                            return `vendor-${firstChar}`;
                        }
                    }
                    
                    return 'main';
                }
            }
        }
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'pinia'],
        exclude: ['panadero-minigames']
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
