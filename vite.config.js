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
                manualChunks(id) {
                    // Chunk for Vue and related packages
                    if (id.includes('node_modules/vue') || 
                        id.includes('node_modules/@vue') || 
                        id.includes('node_modules/@inertiajs/vue3')) {
                        return 'vendor-vue';
                    }
                    
                    // Chunk for other vendor packages
                    if (id.includes('node_modules/')) {
                        return 'vendor';
                    }

                    // Games chunk
                    if (id.includes('panadero-minigames')) {
                        return 'games';
                    }

                    // Dashboard chunk
                    if (id.includes('/pages/Dashboard.vue') || 
                        id.includes('/pages/ecommerce/EcommerceDashboard.vue') ||
                        id.includes('/pages/erp/ErpDashboard.vue') ||
                        id.includes('/pages/dashboard1/cards/')) {
                        return 'dashboard';
                    }

                    // Profile chunk
                    if (id.includes('/pages/Profile/')) {
                        return 'profile';
                    }

                    // Teams chunk
                    if (id.includes('/pages/Teams/')) {
                        return 'teams';
                    }

                    // Ecommerce chunk
                    if (id.includes('/pages/ecommerce/') ||
                        id.includes('/pages/Storefront.vue')) {
                        return 'ecommerce';
                    }
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
