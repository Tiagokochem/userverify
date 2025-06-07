<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ExternalApiService;
use Illuminate\Http\Client\Response;

class ExternalApiServiceTest extends TestCase
{
    public function test_fetch_all_success(): void
    {
        // Arrange: fakes HTTP responses
        Http::fake([
            'https://viacep.com.br/ws/*' => Http::response(['cep'=>'06454-000'], 200),
            'https://api.nationalize.io*' => Http::response(['name'=>'lucas'], 200),
        ]);

        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('info')->atLeast()->once();

        $service = new ExternalApiService();

        // Act
        $result = $service->fetchAll('12345678900', '06454000', 'lucas.silva@example.com');

        // Assert
        $this->assertArrayHasKey('viacep', $result);
        $this->assertArrayHasKey('nationalize', $result);
        $this->assertEquals('12345678900', $result['cpf']);
        $this->assertEquals('06454000', $result['cep']);
        $this->assertEquals('lucas.silva@example.com', $result['email']);
        $this->assertArrayHasKey('cpf_status', $result);
    }

    public function test_fetch_all_error_returns_error_array(): void
    {
        // Arrange: force HTTP error
        Http::fake([
            'https://viacep.com.br/ws/*' => Http::response(null, 500),
            'https://api.nationalize.io*' => Http::response(null, 500),
        ]);

        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $service = new ExternalApiService();

        // Act
        $result = $service->fetchAll('12345678900', '06454000', 'lucas@example.com');

        // Assert
        $this->assertTrue($result['error']);
        $this->assertEquals('external_api_error', $result['message']);
        $this->assertEquals('12345678900', $result['cpf']);
    }
} 