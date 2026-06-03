import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  root: 'admin-app',
  build: {
    outDir: resolve(__dirname, 'dist/admin'),
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(__dirname, 'admin-app/main.jsx'),
      output: {
        entryFileNames: 'wordpify-admin.js',
        chunkFileNames: 'wordpify-admin-[name].js',
        assetFileNames: (info) => {
          if (info.name?.endsWith('.css')) return 'wordpify-admin.css';
          return '[name][extname]';
        },
      },
    },
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'admin-app'),
    },
  },
});
