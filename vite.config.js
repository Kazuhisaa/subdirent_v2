
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/bootstrap/css/bootstrap.css',
                'resources/bootstrapjs/js/bootstrap.bundle.js',
                'resources/css/admin.css',
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
