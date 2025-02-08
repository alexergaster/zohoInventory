<?php

namespace App\Services\SaleOrder;

use App\Services\InventoryRequest;
use DateTime;
use Exception;

class Service
{
    use InventoryRequest;

    private const vendorId = 701659000000063361;

    public function __construct()
    {
        $this->accessToken = config('services.zoho.access_token');
        $this->refreshToken = config('services.zoho.refresh_token');
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
    }

    private function formatDate(string $date): string
    {
        return DateTime::createFromFormat('d.m.Y', $date)->format('Y-m-d');
    }

    /**
     * @throws Exception
     */
    private function purchaseOrders(array $data): void
    {
        $items = $data['line_items'];
        $purchaseData = ["vendor_id" => self::vendorId, "line_items" => []];

        $itemIds = "";

        foreach ($items as $item) {
            $itemIds .= $item['item_id'] . ',';
        }

        $response = $this->sendRequest("/itemdetails", 'get', [], ['item_ids' => $itemIds]);

        foreach ($items as $item) {
            if ($response['items'][0]['available_stock'] < $item['quantity']) {
                $purchaseData['line_items'][] = [
                    "item_id" => $item['item_id'],
                    "quantity" => $item['quantity'] - $response['items'][0]['available_stock'],
                ];
            }
        }

        $this->sendRequest('/purchaseorders', 'post', $purchaseData);
    }

    /**
     * @throws Exception
     */
    public function store(array $data): array
    {
        if ($data['is_purchase_order']) {
            $this->purchaseOrders($data);
        }

        unset($data['is_purchase_order']);

        $data['date'] = $this->formatDate($data['date']);

        return $this->sendRequest('/salesorders', 'post', $data);
    }
}
