<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AiPredictionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8000');
    }

    public function getEstimatedWaitingTime(array $ticketData): ?float
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/predict", [
                'queue_length_before'   => $ticketData['queue_length_before'],
                'avg_service_time'      => $ticketData['avg_service_time'],
                'available_staff'       => $ticketData['available_staff'],
                'previous_waiting_time' => $ticketData['previous_waiting_time'],
                'hour'                  => $ticketData['hour'],
                'day_of_week'           => $ticketData['day_of_week'],
                'service_type'          => $ticketData['service_type'],
            ]);

            if ($response->successful()) {
                return $response->json('predicted_waiting_time_minutes');
            }
            return null;

        } catch (Exception $e) {
            Log::error('Failed to connect to AI Service: ' . $e->getMessage());
            return null; 
        }
    }
}