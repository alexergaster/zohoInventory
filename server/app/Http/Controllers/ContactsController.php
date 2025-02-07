<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contacts\StoreRequest;
use App\Services\Contacts\Service;
use Exception;
use Illuminate\Http\JsonResponse;


class ContactsController extends Controller
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index():JsonResponse
    {
        $response = $this->service->index();

        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * @throws Exception
     */
    public function store(StoreRequest $request):JsonResponse
    {
        $data = $request->validated();

        $response = $this->service->store($data);

        return response()->json(['success' => true, 'data' => $response]);
    }
}
