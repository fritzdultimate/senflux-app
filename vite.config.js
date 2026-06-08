import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/nav.js'
            ],
            refresh: [
                'resources/views/**',
                'app/Livewire/**',
            ],
        }),
    tailwindcss(),
  ],
})