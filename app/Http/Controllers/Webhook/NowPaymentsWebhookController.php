<?php

namespace App\Http\Controllers\Webhook;

use App\Services\DepositService;
use App\Services\NowPaymentsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class NowPaymentsWebhookController extends Controller {
    public function __construct(
        private NowPaymentsService $nowPayments,
        private DepositService $depositService,
        private SubscriptionService $subscriptionService,
    ) {}

    public function handle(Request $request): Response {
        $rawBody   = $request->getContent();
        $signature = $request->header('x-nowpayments-sig', '');

        if (!app()->isLocal() && !$this->nowPayments->verifyIpnSignature($rawBody, $signature)) {
            Log::warning('NowPayments IPN signature verification failed', [
                'ip' => $request->ip(),
            ]);
            return response('Unauthorized', 401);
        }

        $payload = $request->json()->all();
        $orderId = $payload['order_id'] ?? '';

        Log::info('NowPayments IPN received', [
            'payment_id'     => $payload['payment_id'] ?? null,
            'payment_status' => $payload['payment_status'] ?? null,
            'order_id'       => $orderId,
        ]);

        try {
            if (str_starts_with($orderId, 'SUB-')) {
                $this->subscriptionService->handleIpnUpdate($payload);
            } else {
                $this->depositService->handleIpnUpdate($payload);
            }
        } catch (\Exception $e) {
            Log::error('NowPayments IPN processing error', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
        }

        return response('OK', 200);
    }
}