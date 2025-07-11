import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { useSessionStore } from '@/stores/session';

const pinia = createPinia();
const appName = import.meta.env.VITE_APP_NAME || 'indigo3';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);
        
        // Initialize session store globally
        const sessionStore = useSessionStore();
        
        app.mount(el);
        sessionStore.initializeSession();
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
