<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleOrder\StoreRequest;
use App\Services\SaleOrder\Service;
use Exception;
use Illuminate\Http\JsonResponse;

class SaleOrderController extends Controller
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->service->store($data);

        return response()->json(["success" => true, "data" => $response]);
    }
}
