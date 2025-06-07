<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogHttpRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Gera um correlation_id único para esta requisição
        $correlationId = (string) Str::uuid();
        // Inclui no contexto global de logs
        Log::withContext(['correlation_id' => $correlationId]);

        // Log de entrada
        Log::channel('stack')->info('[Request] Início', [
            'method'       => $request->method(),
            'uri'          => $request->path(),
            'payload'      => $request->except(['password','password_confirmation']),
            'correlation_id' => $correlationId,
        ]);

        // Executa a request
        $response = $next($request);

        // Log de saída
        Log::channel('stack')->info('[Response] Fim', [
            'status'        => $response->status(),
            'correlation_id'=> $correlationId,
        ]);

        return $response;
    }
}
