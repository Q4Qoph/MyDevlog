import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const appUrl = process.env.APP_URL ?? 'http://localhost';
const { origin, pathname } = new URL(appUrl);
const base = `${origin}${pathname.replace(/\/?$/, '/')}`;

export default defineConfig({
    base,
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
