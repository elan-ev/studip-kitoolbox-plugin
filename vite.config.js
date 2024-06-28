import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
    return {
        plugins: [vue()],
        build: {
            rollupOptions: {
              input: {
                kitoolbox: 'src/kitoolbox.js',
                'kitoolbox-admin': 'src/kitoolbox-admin.js',
              },
                output: {
                    entryFileNames: `[name].js`,
                    assetFileNames: (assetInfo) => {
                        if (assetInfo.name == 'style.css') return 'kitoolbox.css';
                        return assetInfo.name;
                    },
                },
            },
        },
        define: { 'process.env.NODE_ENV': `"${mode}"` },
    };
});
