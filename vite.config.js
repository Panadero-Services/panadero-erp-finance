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
        chunkSizeWarningLimit: 4000, // Increase limit temporarily
        minify: 'esbuild',
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Group by major features
                    if (id.includes('/pages/dashboard1/') || 
                        id.includes('/pages/Dashboard.vue') ||
                        id.includes('/pages/ecommerce/EcommerceDashboard.vue') ||
                        id.includes('/pages/erp/ErpDashboard.vue')) {
                        return 'feature-dashboard';
                    }

                    if (id.includes('/pages/ecommerce/') || 
                        id.includes('/pages/Storefront.vue')) {
                        return 'feature-ecommerce';
                    }

                    if (id.includes('/pages/admin/')) {
                        return 'feature-admin';
                    }

                    if (id.includes('/pages/Teams/')) {
                        return 'feature-teams';
                    }

                    if (id.includes('/pages/Profile/')) {
                        return 'feature-profile';
                    }

                    if (id.includes('/pages/home/')) {
                        return 'feature-home';
                    }

                    // Games
                    if (id.includes('panadero-minigames')) {
                        return 'feature-games';
                    }

                    // Vendor chunks
                    if (id.includes('node_modules/vue') || 
                        id.includes('node_modules/@vue') || 
                        id.includes('node_modules/@inertiajs')) {
                        return 'vendor-vue';
                    }

                    if (id.includes('node_modules/')) {
                        return 'vendor';
                    }

                    // Default chunk
                    return 'main';
                }
            }
        }
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'pinia']
    }
});
