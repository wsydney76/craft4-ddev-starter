import '@css/app.scss';
// import './scripts';

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log("HMR")
    });
}

// BaguetteBox gallery lightbox
import baguetteBox from 'baguettebox.js';
window.baguetteBox = baguetteBox;

// Alpine JS store for Cookie Consent
import Consent from './stores/Consent';

import Cookies from 'js-cookie'
window.Cookies = Cookies

// Alpine JS
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import focus from '@alpinejs/focus'

window.Alpine = Alpine
Alpine.plugin(collapse)
Alpine.plugin(focus)

Alpine.store('consent', Consent)

Alpine.start()


