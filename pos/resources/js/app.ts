import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from 'primevue/config';
import { createApp, h } from 'vue';
import type { DefineComponent } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Nineties Coffee POS';
const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent<DefineComponent>(`./pages/${name}.vue`, pages),
    progress: {
        color: '#D4AF37',
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, { unstyled: true })
            .mount(el);
    },
});
