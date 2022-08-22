import '@css/app.css';

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

window.Alpine = Alpine
Alpine.plugin(collapse)
Alpine.start()


