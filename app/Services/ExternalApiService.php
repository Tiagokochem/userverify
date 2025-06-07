<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalApiService
{
    /**
     * Chama as APIs externas e retorna um array estruturado com:
     * - cpf, cep, email
     * - dados do ViaCep
     * - dados do Nationalize
     * - status do CPF (mockado)
     */
    public function fetchAll(string $cpf, string $cep, string $email): array
    {
        // 1) Log de início
        Log::channel('stack')->info('[ExternalApi] Iniciando fetchAll', compact('cpf','cep','email'));

        // Extrai primeiro nome para a nationalize.io
        $firstName = explode('@', $email)[0];
        $firstName = explode('.', $firstName)[0];

        try {
            // 2) Pool de requisições com retry
            $responses = Http::pool(fn($pool) => [
                $pool->withOptions(['timeout' => 5])->retry(3, 500)
                     ->get("https://viacep.com.br/ws/{$cep}/json/"),
                $pool->withOptions(['timeout' => 5])->retry(3, 500)
                     ->get("https://api.nationalize.io", ['name' => $firstName]),
            ]);

            /** @var Response $viaCepRes */
            /** @var Response $nationalizeRes */
            [$viaCepRes, $nationalizeRes] = $responses;

            // 3) Log de cada resposta
            Log::channel('stack')->info('[ExternalApi] ViaCep retornou', [
                'status' => $viaCepRes->status(),
                'ok'     => $viaCepRes->successful(),
            ]);
            Log::channel('stack')->info('[ExternalApi] Nationalize retornou', [
                'status' => $nationalizeRes->status(),
                'ok'     => $nationalizeRes->successful(),
            ]);

            // 4) Valida sucesso
            if (! $viaCepRes->successful() || ! $nationalizeRes->successful()) {
                throw new \Exception('external_api_error');
            }

            // 5) Mock do status do CPF
            $statuses = ['limpo', 'pendente', 'negativado'];
            $idx = hexdec(substr(sha1($cpf), 0, 2)) % count($statuses);
            $cpfStatus = $statuses[$idx];

            Log::channel('stack')->info('[ExternalApi] fetchAll concluído', ['cpf_status'=>$cpfStatus]);

            // 6) Retorno completo, incluindo CPF
            return [
                'cpf'         => $cpf,
                'cep'         => $cep,
                'email'       => $email,
                'viacep'      => $viaCepRes->json(),
                'nationalize' => $nationalizeRes->json(),
                'cpf_status'  => ['status' => $cpfStatus],
            ];

        } catch (Throwable $e) {
            // 7) Log de erro
            Log::channel('stack')->error('[ExternalApi] Erro fetchAll', [
                'message' => $e->getMessage(),
                'cpf'     => $cpf,
            ]);

            // Retorna estrutura de erro
            return ['error' => true, 'message' => 'external_api_error', 'cpf'=>$cpf];
        }
    }
}
