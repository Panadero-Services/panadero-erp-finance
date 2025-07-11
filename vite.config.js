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
        chunkSizeWarningLimit: 3000,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core vendor chunks
                    'vendor-vue': ['vue', '@inertiajs/vue3', '@vue/runtime-core', '@vue/runtime-dom'],
                    'vendor-other': ['axios', 'lodash'],
                    
                    // Main feature chunks
                    'dashboard': [
                        './resources/js/pages/Dashboard.vue',
                        './resources/js/pages/ecommerce/EcommerceDashboard.vue',
                        './resources/js/pages/erp/ErpDashboard.vue'
                    ],
                    'profile': [
                        './resources/js/pages/Profile/Show.vue',
                        './resources/js/pages/Profile/Partials/UpdateProfileInformationForm.vue',
                        './resources/js/pages/Profile/Partials/UpdatePasswordForm.vue'
                    ],
                    'teams': [
                        './resources/js/pages/Teams/Create.vue',
                        './resources/js/pages/Teams/Show.vue'
                    ],
                    'ecommerce': [
                        './resources/js/pages/Storefront.vue',
                        './resources/js/pages/ecommerce/ProductOverview.vue',
                        './resources/js/pages/ecommerce/CategoryOverview.vue'
                    ],
                    'games': [
                        './resources/js/panadero/panadero-minigames/othello.vue',
                        './resources/js/panadero/panadero-minigames/breakout.vue'
                    ]
                }
            }
        }
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'pinia']
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
