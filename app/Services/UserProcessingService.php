<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserProcessingService
{
    public function handle(array $data): array
    {
        $cpf = $data['cpf'];

        $cacheKey = "user:cpf:$cpf";

        // Verifica se já está cacheado
        if (Cache::tags(['users'])->has($cacheKey)) {
            Log::info("[UserProcessing] Cache hit para CPF $cpf");

            $cached = Cache::tags(['users'])->get($cacheKey);

            return [
                'data' => [
                    'status' => 'cached',
                    'data' => $cached,
                ],
                'status' => 200
            ];
        }

        Log::info("[UserProcessing] Cache miss para CPF $cpf");

        // Retorno temporário para testar
        return [
            'data' => [
                'status' => 'processing',
                'input' => $data,
            ],
            'status' => 200
        ];
    }
}
