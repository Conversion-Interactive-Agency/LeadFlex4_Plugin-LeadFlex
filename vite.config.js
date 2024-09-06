// Vite Plugins
import ViteRestart from "vite-plugin-restart";
import {ViteImageOptimizer} from "vite-plugin-image-optimizer";
import eslintPlugin from "vite-plugin-eslint";
import stylelintPlugin from "vite-plugin-stylelint";

// PostCSS
import autoprefixer from "autoprefixer";

// Custom (for the moment) - Pulled from Verbb Formie project
import ImageminCopy from "./src/assets/vite-plugins/imagemin-copy";
import StaticCopy from "./src/assets/vite-plugins/static-copy";
import IifeWrap from "./src/assets/vite-plugins/iife-wrap";

import {resolve} from "path";

const apps = [
    "site",
    "cp"
];

const entries = {};
apps.forEach(app => (entries[app] = resolve(__dirname, `src/assets/${app}/js/${app}.js`)));

const styleLint = stylelintPlugin;

export default ({command}) => ({
    build: {
        outDir: "src/assets/dist",
        emptyOutDir: true,
        manifest: false,
        sourcemap: true,
        rollupOptions: {
            input: {...entries},
            output: {
                entryFileNames: "js/[name].js",
                chunkFileNames: "js/[name].js",
                assetFileNames: "css/[name].[ext]",
            },
        },
    },
    plugins: [
        // Custom plugins (for the moment)
        ImageminCopy,
        StaticCopy,

        ViteRestart({
            reload: [
                "./src/assets/**/scss/*.scss",
                "./src/templates/**/*.twig"
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

        // Wrap the build files in a self invoking function
        IifeWrap
    ],
});
