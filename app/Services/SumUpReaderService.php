<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SumUpReaderService
{
    protected string $apiKey;
    protected string $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.sumup.api_key');
        $this->baseUrl = config('services.sumup.base_url');
    }

    /**
     * Get the reader for merchant (assumes only one reader)
     */
    public function getReader(string $merchantId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/merchants/{$merchantId}/readers");

            if ($response->successful()) {
                $readers = $response->json() ?? [];
                
                // Return the first reader or null if none exist
                return !empty($readers['items']) ? $readers['items'][0] : null;
            }

            $this->handleError($response);
            return null;

        } catch (\Exception $e) {
            Log::error('Failed to fetch SumUp reader', [
                'merchant_id' => $merchantId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get reader with caching (5 minutes)
     */
    public function getReaderCached(string $merchantId): ?array
    {
        $cacheKey = "sumup_reader_{$merchantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($merchantId) {
            return $this->getReader($merchantId);
        });
    }

    /**
     * Check if merchant has a reader
     */
    public function hasReader(string $merchantId): bool
    {
        return $this->getReaderCached($merchantId) !== null;
    }

    /**
     * Get reader ID for merchant
     */
    public function getReaderId(string $merchantId): ?string
    {
        $reader = $this->getReaderCached($merchantId);
        return $reader['id'] ?? null;
    }

    /**
     * Get device ID for merchant
     */
    public function getDeviceId(string $merchantId): ?string
    {
        $reader = $this->getReaderCached($merchantId);
        return $reader['device']['identifier'] ?? null;
    }

    /**
     * Clear reader cache
     */
    public function clearCache(string $merchantId): void
    {
        Cache::forget("sumup_reader_{$merchantId}");
    }

    /**
     * Trigger payment on reader
     */
    public function triggerPayment(string $merchantId, float $amount, string $currency = 'GBP'): ?array
    {
        if (!$this->hasReader($merchantId)) {
            Log::warning('No SumUp reader found for merchant', ['merchant_id' => $merchantId]);
            return null;
        }

        $deviceId = $this->getDeviceId($merchantId);
        if (!$deviceId) {
            Log::warning('Failed to get SumUp device ID for merchant', ['merchant_id' => $merchantId]);
            return null;
        }

        // convert amount to whole number of minor units (e.g., cents)
        // $amountMinorUnits = (int) round($amount * 100);
        $amountMinorUnits = (int) ($amount * 100);

        $readerId = $this->getReaderId($merchantId);
         try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/merchants/{$merchantId}/readers/{$readerId}/checkout", [
                'total_amount' => [
                    'value' => $amountMinorUnits,
                    'currency' => $currency,
                    'minor_unit' => '2'
                ]
            ]);

            if ($response->successful()) {
                $checkout = $response->json() ?? [];
                return $checkout;
            }

            $this->handleError($response);
            return null;

        } catch (\Exception $e) {
            Log::error('Failed to create checkout', [
                'merchant_id' => $merchantId,
                'device_id' => $deviceId,
                'reader_id' => $readerId,
                'amount' => $amount,
                'currency' => $currency,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }   

    /**
     * Retrieve checkout/payment status by client transaction id
     */
    public function getCheckoutStatus(string $merchantId, string $clientTransactionId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/merchants/{$merchantId}/transactions");


            dd($response->json());
            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $this->handleError($response);
            return null;

        } catch (\Exception $e) {
            Log::error('Failed to fetch SumUp checkout status', [
                'merchant_id' => $merchantId,
                'client_transaction_id' => $clientTransactionId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Handle API errors
     */
    protected function handleError($response): void
    {
        $error = $response->json();
        
        Log::warning('SumUp API Error', [
            'status' => $response->status(),
            'error' => $error,
        ]);
    }
}