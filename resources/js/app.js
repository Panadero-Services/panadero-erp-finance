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
    resolve: (name) => {
        // Let Inertia handle the lazy loading
        const pages = import.meta.glob('./pages/**/*.vue', { eager: false });
        return pages[`./pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);
        
        const sessionStore = useSessionStore();
        app.mount(el);
        sessionStore.initializeSession();
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
