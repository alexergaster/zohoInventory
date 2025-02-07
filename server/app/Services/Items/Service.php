<?php

namespace App\Services\Items;

use App\Services\InventoryRequest;
use Exception;

class Service
{
    use InventoryRequest;
    private const itemType = "inventory";

    public function __construct()
    {
        $this->accessToken = config('services.zoho.access_token');
        $this->refreshToken = config('services.zoho.refresh_token');
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
    }

    /**
     * @throws Exception
     */
    public function index(): array
    {
        return $this->sendRequest('/items', "get");
    }

    /**
     * @throws Exception
     */
    public function store(array $data): array
    {
        $data['item_type'] = self::itemType;

        return $this->sendRequest('/items', "post", $data);
    }
}
