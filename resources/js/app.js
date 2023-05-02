import '@css/app.scss';

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log("HMR")
    });
}

// BaguetteBox gallery lightbox
import baguetteBox from 'baguettebox.js';
window.baguetteBox = baguetteBox;


// Alpine JS
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import focus from '@alpinejs/focus'
import morph from '@alpinejs/morph'

window.Alpine = Alpine
Alpine.plugin(collapse)
Alpine.plugin(focus)
Alpine.plugin(morph)
Alpine.start()


