import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      buildDirectory: 'vendor/butler',
      input: [
        'resources/js/app.js',
        'resources/css/app.css',
      ],
      refresh: true,
    }),
  ],
  resolve: {
    alias: [
      {find: /^~/, replacement: ''}
    ],
  },
});
