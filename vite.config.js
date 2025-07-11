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
        chunkSizeWarningLimit: 2500, // Increase limit further if needed
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Group panadero game files
                    if (id.includes('panadero/panadero-minigames/')) {
                        return 'panadero-games';
                    }
                    // Group node_modules into vendor chunk
                    if (id.includes('node_modules')) {
                        if (id.includes('vue')) {
                            return 'vendor-vue';
                        }
                        return 'vendor';
                    }
                    // Group components
                    if (id.includes('/components/')) {
                        return 'components';
                    }
                    // Group pages
                    if (id.includes('/pages/')) {
                        return 'pages';
                    }
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
