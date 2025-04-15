import './bootstrap';
import '../css/app.css';

import "/vendor/dhx_suite/codebase/suite.js?v=9.1.3";
import "/vendor/dhx_suite/codebase/suite.css?v=9.1.3";


//import "/vendor/MaterialDesign/materialdesignicons.css?v=6.4.2";


// gridpack
//import "dhx-gridpack-package/codebase/gridpack.js";
//import "dhtmlx-gridpack/codebase/gridpack.css";

// kanban
//import { Kanban, Toolbar, defaultEditorShape } from "@dhx/trial-kanban/dist/kanban.js";
//import "@dhx/trial-kanban/dist/kanban.css";

import { createApp, h } from 'vue'; 
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
const pinia = createPinia()

const appName = import.meta.env.VITE_APP_NAME || 'indigo3';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
