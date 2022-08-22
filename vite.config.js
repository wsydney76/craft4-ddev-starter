import path from 'path';
import ViteRestart from 'vite-plugin-restart';
import manifestSRI from 'vite-plugin-manifest-sri';
import viteCompression from 'vite-plugin-compression';

// https://vitejs.dev/config/
/**
 * @type {import('vite').UserConfig}
 */
export default ({ command }) => {
    return {
        base: command === 'serve' ? '' : '/assets/dist/',
        build: {
            commonjsOptions: {
                transformMixedEsModules: true,
            },
            manifest: true,
            outDir: path.resolve(__dirname, 'web/assets/dist/'),
            assetsDir: './',
            rollupOptions: {
                input: {
                    app: path.resolve(__dirname, 'resources/js/app.js'),
                },
            }
        },
        plugins: [
            manifestSRI(),
            viteCompression({
                filter: /\.(js|mjs|json|css|map)$/i
            }),
            ViteRestart({
                reload: [
                    path.resolve(__dirname, 'templates/**/*')
                ],
            }),
        ],
        publicDir: path.resolve(__dirname, 'resources/public'),
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources'),
                '@css': path.resolve(__dirname, 'resources/css'),
                '@js': path.resolve(__dirname, 'resources/js'),
            },
        },
        server: {
            host: '0.0.0.0',
            port: 3000,
            strictPort: true,
        },
    };
};
