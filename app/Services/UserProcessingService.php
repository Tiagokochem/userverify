<?php

namespace App\Services;

use App\Jobs\AnalyzeUserJob;
use App\Models\UserRecord;
use Illuminate\Support\Facades\Cache;

class UserProcessingService
{
    public function __construct(protected ExternalApiService $externalApi) {}

    public function handle(array $data): array
    {
        $cpf = $data['cpf'];
        $cacheKey = "user:cpf:$cpf";

        // cache hit
        if (Cache::tags(['users'])->has($cacheKey)) {
            $cached = Cache::tags(['users'])->get($cacheKey);
            return [
                'data'   => ['status' => 'cached', 'data' => $cached],
                'status' => 200,
            ];
        }

        // chama as APIs
        $apiData = $this->externalApi->fetchAll($cpf, $data['cep'], $data['email']);
        if (isset($apiData['error'])) {
            return [
                'data'   => ['status' => 'external_api_error'],
                'status' => 503,
            ];
        }

        // salva no banco (updateOrCreate cobre repetição de CPF)
        $record = UserRecord::updateOrCreate(
            ['cpf' => $cpf],
            [
                'cep'           => $data['cep'],
                'email'         => $data['email'],
                'external_data' => json_encode($apiData),
            ]
        );

        // cacheia o array de dados integrados
        Cache::tags(['users'])->put($cacheKey, $apiData, now()->addDay());

        // dispara o job de análise assíncrona
        AnalyzeUserJob::dispatch($record);

        return [
            'data'   => [
                'status' => 'ok',
                'source' => 'api',
                'data'   => $apiData,
            ],
            'status' => 200,
        ];
    }
}
