<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    public function fetchAll(string $cpf, string $cep, string $email): array
    {
        // Extrai primeiro nome
        $firstName = explode('@', $email)[0];
        $firstName = explode('.', $firstName)[0];

        try {
            // Chama só as APIs externas reais
            $responses = Http::pool(fn($pool) => [
                $pool->withOptions(['timeout' => 5])->retry(3, 1000)
                     ->get("https://viacep.com.br/ws/$cep/json/"),
                $pool->withOptions(['timeout' => 5])->retry(3, 1000)
                     ->get("https://api.nationalize.io", ['name' => $firstName]),
            ]);

            [$viaCepRes, $nationalizeRes] = $responses;

            foreach ([$viaCepRes, $nationalizeRes] as $res) {
                if (! $res instanceof Response || ! $res->successful()) {
                    throw new \Exception('external_api_error');
                }
            }

            // Gera o mock do status do CPF em linha de código:
            $statuses = ['limpo', 'pendente', 'negativado'];
            $idx = hexdec(substr(sha1($cpf), 0, 2)) % count($statuses);
            $cpfStatus = $statuses[$idx];

            return [
                'viacep'     => $viaCepRes->json(),
                'nationalize'=> $nationalizeRes->json(),
                'cpf_status' => ['status' => $cpfStatus],
            ];

        } catch (\Throwable $e) {
            Log::error('[ExternalApi] Erro ao chamar APIs externas', [
                'message' => $e->getMessage(),
            ]);

            return ['error' => true, 'message' => 'external_api_error'];
        }
    }
}
