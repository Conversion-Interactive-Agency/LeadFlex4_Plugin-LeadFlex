// Vite Plugins
import ViteRestart from "vite-plugin-restart";
import { ViteImageOptimizer } from "vite-plugin-image-optimizer";
import eslintPlugin from "vite-plugin-eslint";
import stylelintPlugin from "vite-plugin-stylelint";

// PostCSS
import autoprefixer from "autoprefixer";

// Custom (for the moment) - Pulled from Verbb Formie project
import ImageminCopy from "./vite-plugins/imagemin-copy";
import StaticCopy from "./vite-plugins/static-copy";

import { resolve } from "path";

const apps = [
  "site",
  // 'cp',
];

const entries = {};
apps.forEach(app => (entries[app] = resolve(__dirname, `${app}/src/js/${app}.js`)));

const styleLint = stylelintPlugin;

export default ({ command }) => ({
  build: {
    outDir: "dist",
    emptyOutDir: true,
    manifest: false,
    sourcemap: true,
    rollupOptions: {
      input: { ...entries },
      output: {
        entryFileNames: "js/[name].js",
        chunkFileNames: "js/[name].js",
        assetFileNames: "css/[name].[ext]",
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
        "./scss/*.scss",
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
