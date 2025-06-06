<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserProcessRequest;
use App\Services\UserProcessingService;
use Illuminate\Http\JsonResponse;

class UserProcessController extends Controller
{
    protected UserProcessingService $service;

    public function __construct(UserProcessingService $service)
    {
        $this->service = $service;
    }

    public function process(UserProcessRequest $request): JsonResponse
    {
        $result = $this->service->handle($request->validated());

        return response()->json($result['data'], $result['status']);
    }
}
