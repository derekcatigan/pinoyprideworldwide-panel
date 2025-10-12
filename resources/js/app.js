import './bootstrap';
import '../css/app.css'
import { createApp, h } from 'vue'
import { ZiggyVue } from 'ziggy-js'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'

createInertiaApp({
    title: (title) => title ? title : "Pinoy Pride Worldwide LLC - Panel",
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el)
    },
})