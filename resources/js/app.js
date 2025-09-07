import './bootstrap';
import '../css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { useSessionStore } from '@/stores/session';
// import SolarSysGame from 'panadero-solarsysinvaders';

const pinia = createPinia();
const appName = import.meta.env.VITE_APP_NAME || 'indigo3';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: false });
        return pages[`./pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);
        
        // Register the game component globally
        // app.component('SolarSysGame', SolarSysGame);
        
        // Add timeout and error handling for session initialization
        const initApp = async () => {
            // Check if this is a public route that doesn't need session management
            const currentPath = window.location.pathname;
            const publicRoutes = ['/indigo3/inventory', '/login', '/register'];
            
            if (publicRoutes.includes(currentPath)) {
                console.log('Public route detected, skipping session initialization');
                app.mount(el);
                return;
            }
            
            // Only create session store for protected routes
            const sessionStore = useSessionStore();
            
            try {
                await Promise.race([
                    sessionStore.ensureFreshToken(),
                    new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Session init timeout')), 5000)
                    )
                ]);
                app.mount(el);
                sessionStore.initializeSession();
            } catch (error) {
                console.warn('Session initialization failed, mounting app anyway:', error);
                app.mount(el);
            }
        };
        
        initApp();
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
