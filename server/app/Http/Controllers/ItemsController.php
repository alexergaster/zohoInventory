<?php

namespace App\Http\Controllers;

use App\Http\Requests\Items\StoreRequest;
use App\Services\Items\Service;
use Exception;
use Illuminate\Http\JsonResponse;

class ItemsController extends Controller
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index(): JsonResponse
    {
        $response = $this->service->index();

        return response()->json(['success' => true, 'data' => $response]);
    }

//    public function store(StoreRequest $request): JsonResponse
//    {
//        $data = $request->validated();
//
//        $response = $this->service->addItem($data);
//
//        return response()->json(['success' => true, 'data' => $response]);
//    }
}
