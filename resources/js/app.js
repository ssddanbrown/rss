import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Monkey-Patching of 'URL' constructor to use URL set via the `<base>` tag since
// inertia.js does not seem to have a dynamic way to set a base URL or use base tags.
(function(nativeURL) {
    const configuredBase = document.querySelector('base').href;
    window.URL = function(url, base) {
        if (base === window.location.toString()) {
            base = configuredBase;
        }
        return new nativeURL(url, base);
    }
})(URL);

createInertiaApp({
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .component('Link', Link)
            .use(plugin)
            .mount(el)
    },
});
