// Vite Plugins
import ViteRestart from 'vite-plugin-restart';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';
import eslintPlugin from 'vite-plugin-eslint'
import stylelintPlugin from 'vite-plugin-stylelint';

// PostCSS
import autoprefixer from 'autoprefixer'

// Custom (for the moment) - Pulled from Verbb Formie project
import ImageminCopy from './src/vite-plugins/imagemin-copy';
import StaticCopy from './src/vite-plugins/static-copy';

const styleLint = stylelintPlugin

export default ({ command }) => ({
    build: {
        outDir: './dist',
        emptyOutDir: true,
        manifest: false,
        sourcemap: true,
        rollupOptions: {
            input: {
                'leadflex-cp': 'src/js/app.js',
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: 'css/[name].[ext]',
            },
        },
    },
    css: {
        postcss: {
            plugins: [
                autoprefixer({})
            ],
        }
    },

    plugins: [
        // Custom plugins (for the moment)
        ImageminCopy,
        StaticCopy,

        ViteRestart({
            reload: [
                './scss/*.scss',
            ],
        }),

        eslintPlugin({
            fix: true,
        }),

        // todo: Add stylelintPlugin
        // stylelintPlugin({
        //     fix: true,
        //     include: './scss/*.scss'
        // }),

        ViteImageOptimizer(),
    ],
});

