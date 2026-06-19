import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/dashboard.css', 
                'resources/css/dashboard-shared.css', 
                'resources/css/billing.css', 
                'resources/css/dc.css', 
                'resources/css/deposit.css', 
                'resources/css/deposit-additions.css', 
                'resources/css/withdraw.css', 

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