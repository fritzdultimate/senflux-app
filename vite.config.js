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
            'resources/css/affiliate.css', 
            'resources/css/wallet.css',
            'resources/css/rank-rewards.css',
            'resources/css/portfolio.css',
            'resources/css/markets.css',
            'resources/css/alerts.css',
            'resources/css/settings.css',
            'resources/css/trading-bots.css',
            'resources/css/my-bots.css',
            'resources/css/live-trades.css',
            'resources/css/signals.css',
            'resources/css/terminal.css',
            'resources/css/formation-detail.css',
            'resources/css/formation-card.css',
            'resources/css/market-insights.css',
            'resources/css/bot-activity.css',

            'resources/js/app.js',
            'resources/js/nav.js',
        ],
        refresh: true,
    }),
    tailwindcss(),
  ],
})