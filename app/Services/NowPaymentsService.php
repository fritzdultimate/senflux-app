<?php

namespace App\Services;

use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsService {
    private string $apiKey;
    private string $ipnSecret;
    private string $baseUrl;

    // public function __construct() {
    //     $settings = Cache::remember(
    //         'payment_settings_nowpayments',
    //         now()->addMinutes(1),
    //         fn () => PaymentSetting::where('provider', 'nowpayments')
    //             ->where('is_active', true)
    //             ->first()
    //     );

    //     $sandbox = config('services.nowpayments.sandbox', true);
    //     $this->apiKey  = $settings?->api_key ?? config('services.nowpayments.api_key', '');
    //     $this->ipnSecret = $settings?->ipn_secret ??  config('services.nowpayments.ipn_secret', '');
    //     $this->baseUrl = 'https://api.nowpayments.io/v1';
    // }

    public function __construct() {
        f;
        $settings = Cache::remember(
            'payment_settings_nowpayments',
            now()->addMinutes(1),
            fn () => PaymentSetting::where('provider', 'nowpayments')
                ->where('is_active', true)
                ->first()
                ?->toArray()
        );

        $sandbox = config('services.nowpayments.sandbox', true);
        $this->apiKey    = $settings['api_key']    ?? config('services.nowpayments.api_key', '');
        $this->ipnSecret = $settings['ipn_secret'] ?? config('services.nowpayments.ipn_secret', '');
        $this->baseUrl   = 'https://api.nowpayments.io/v1';
    }

    /**
     * Create a payment invoice on NowPayments.
     *
     * @return array{payment_id, payment_status, pay_address, pay_amount, pay_currency,
     *               price_amount, price_currency, expiration_estimate_date, invoice_url}
     */
    public function createPayment(
        float $priceAmount,
        string $priceCurrency = 'usd',
        string $payCurrency = 'sol',
        string $orderId = '',
        string $orderDescription = '',
        string $ipnCallbackUrl = '',
        string $successUrl = '',
        string $cancelUrl = '',
    ): array {
        // dd($this->apiKey);
        $response = Http::withHeaders([
            'x-api-key'    => $this->apiKey,
            // 'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/payment", [
            'price_amount'        => $priceAmount,
            'price_currency'      => $priceCurrency,
            'pay_currency'        => $payCurrency,
            'order_id'            => $orderId,
            'order_description'   => $orderDescription,
            'ipn_callback_url'    => $ipnCallbackUrl ?: route('webhooks.nowpayments'),
            'success_url'         => $successUrl,
            'cancel_url'          => $cancelUrl,
            'is_fixed_rate'       => false,
            'is_fee_paid_by_user' => false,
        ]);

        if ($response->failed()) {
            Log::error('NowPayments createPayment failed', [
                'status'   => $response->status(),
                'body'     => $response->json(),
                'order_id' => $orderId,
            ]);
            throw new \RuntimeException('Payment gateway error: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Get payment status from NowPayments.
     */
    public function getPaymentStatus(string $paymentId): array {
        $response = Http::withHeaders(['x-api-key' => $this->apiKey])
            ->get("{$this->baseUrl}/payment/{$paymentId}");

        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch payment status: ' . $response->json('message', 'Unknown'));
        }

        return $response->json();
    }

    /**
     * Get minimum payment amount for a currency.
     */
    public function getMinimumPaymentAmount(string $currency, string $fiatCurrency = 'usd'): float {
        $response = Http::withHeaders(['x-api-key' => $this->apiKey])
            ->get("{$this->baseUrl}/min-amount", [
                'currency_from' => $currency,
                'currency_to'   => $fiatCurrency,
            ]);

        return (float) ($response->json('min_amount') ?? 0);
    }

    /**
     * Get list of available currencies.
     */
    public function getAvailableCurrencies(): array {
        $response = Http::withHeaders(['x-api-key' => $this->apiKey])
            ->get("{$this->baseUrl}/currencies");

        return $response->json('currencies') ?? [];
    }

    /**
     * Verify IPN webhook signature.
     * NowPayments signs with HMAC-SHA512 using your IPN secret.
     */
    public function verifyIpnSignature(string $rawBody, string $receivedSignature): bool {
        if (empty($this->ipnSecret)) {
            Log::warning('NowPayments IPN secret not configured');
            return false;
        }

        $payload = json_decode($rawBody, true);
        if (!$payload) return false;

        // NowPayments sorts keys alphabetically before hashing
        ksort($payload);
        $sortedBody = json_encode($payload);

        $expectedSignature = hash_hmac('sha512', $sortedBody, $this->ipnSecret);

        return hash_equals($expectedSignature, strtolower($receivedSignature));
    }

    /**
     * Estimate conversion: how much crypto for a given USD amount.
     */
    public function estimatePrice(float $amount, string $fromCurrency, string $toCurrency): array {
        $response = Http::withHeaders(['x-api-key' => $this->apiKey])
            ->get("{$this->baseUrl}/estimate", [
                'amount'          => $amount,
                'currency_from'   => $fromCurrency,
                'currency_to'     => $toCurrency,
            ]);

        return $response->json() ?? [];
    }
}
