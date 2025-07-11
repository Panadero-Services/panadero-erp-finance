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
        minify: 'esbuild', // Use esbuild instead of terser
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Bundle all Vue-related code together
                    if (id.includes('node_modules/vue') || 
                        id.includes('node_modules/@vue') || 
                        id.includes('node_modules/@inertiajs')) {
                        return 'vendor-vue';
                    }

                    // Other node_modules
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }

                    // Keep profile components together
                    if (id.includes('/Profile/')) {
                        return 'profile';
                    }

                    // Games
                    if (id.includes('panadero-minigames')) {
                        return 'games';
                    }

                    // Everything else
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
