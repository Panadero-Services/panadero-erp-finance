import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { useSessionStore } from '@/stores/session';
import SolarSysGame from 'panadero-solarsysinvaders';

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
        app.component('SolarSysGame', SolarSysGame);
        
        const sessionStore = useSessionStore();
        
        sessionStore.ensureFreshToken().then(() => {
            app.mount(el);
            sessionStore.initializeSession();
        });
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
