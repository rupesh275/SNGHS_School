<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrintNodeService
{
    private $apiKey;
    private $baseUrl = 'https://api.printnode.com';

    public function __construct()
    {
        $this->apiKey = config('services.printnode.api_key');
    }

    /**
     * Get all computers connected to PrintNode
     */
    public function getComputers()
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/computers');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            \Log::error('PrintNode API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get printers for a specific computer
     */
    public function getPrinters($computerId)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . "/computers/{$computerId}/printers");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            \Log::error('PrintNode API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a print job
     */
    public function createPrintJob($printerId, $pdfUrl)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($this->baseUrl . '/printjobs', [
                    'printerId' => $printerId,
                    'title' => 'Receipt Print',
                    'contentType' => 'pdf_uri',
                    'content' => $pdfUrl,
                    'source' => 'Receipt System'
                ]);
            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('PrintNode API Error: ' . $e->getMessage());
            return null;
        }
    }
} 