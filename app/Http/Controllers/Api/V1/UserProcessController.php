<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserProcessRequest;
use App\Services\UserProcessingService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="User Processing API",
 *     description="API para processamento de usuários"
 * )
 */
class UserProcessController extends Controller
{
    public function __construct(protected UserProcessingService $service) {}

    /**
     * @OA\Post(
     *     path="/api/v1/users/process",
     *     summary="Processa um usuário (validação, integração e cache)",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cpf","cep","email"},
     *             @OA\Property(property="cpf", type="string", example="12345678900"),
     *             @OA\Property(property="cep", type="string", example="06454000"),
     *             @OA\Property(property="email", type="string", example="usuario@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso (cache hit ou processamento)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="source", type="string", example="api"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação falhou",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The cep field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Erro em APIs externas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="external_api_error")
     *         )
     *     )
     * )
     */
    public function process(UserProcessRequest $request): JsonResponse
    {
        $result = $this->service->handle($request->validated());
        return response()->json($result['data'], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{cpf}",
     *     summary="Busca usuário por CPF (com cache)",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="cpf",
     *         in="path",
     *         description="CPF do usuário",
     *         required=true,
     *         @OA\Schema(type="string", example="12345678900")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function show(string $cpf): JsonResponse
    {
        $result = $this->service->find($cpf); // supondo que exista um método find
        return response()->json($result['data'], $result['status']);
    }
}
