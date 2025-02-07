<?php

namespace App\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class Service
{
    use InventoryRequest;

    public function __construct()
    {
        $this->baseUrl = config('services.zoho.base_url');
        $this->baseApiUrl = config('services.zoho.api_base_url');
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
        $this->refreshToken = config('services.zoho.refresh_token');
        $this->accessToken = config('services.zoho.access_token');
        $this->vendorId = config('services.zoho.vendor_id');
    }


    /**
     * @throws Exception
     */
    public function getItems(): array
    {
        return $this->sendRequest('items', "get");
    }

//    /**
//     * @throws Exception
//     */
//    public function refreshToken(): void
//    {
//        $refreshToken = $this->refreshToken;
//        $clientId = $this->clientId;
//        $clientSecret = $this->clientSecret;
//
//        $response = Http::withoutVerifying() // ахутенг SSL
//        ->post("$this->baseUrl/oauth/v2/token?refresh_token=$refreshToken&client_id=$clientId&client_secret=$clientSecret&grant_type=refresh_token")->json();
//
//        if (isset($response['access_token'])) {
//            $this->accessToken = $response['access_token'];
//
//            cache()->put('zoho_access_token', $this->accessToken, 3660);
//        } else {
//            throw new Exception('Не вдалося оновити токен: ' . json_encode($response));
//        }
//    }
//
//    /**
//     * @throws Exception
//     */
//    private function sendRequest(string $endpoint, string $method = "post", array $data = [], bool $retry = true): array
//    {
//        $this->accessToken = cache('zoho_access_token') ?? $this->accessToken;
//
//        $headers = [
//            'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken,
//        ];
//        try {
//            $response = Http::withoutVerifying()
//                ->withHeaders($headers)
//                ->$method("https://www.zohoapis.eu/inventory/v1/$endpoint", $data)
//                ->json();
//
//            if (isset($response['code']) && $response['code'] === 57 && $retry) {
//                $this->refreshToken();
//
//                return $this->sendRequest($endpoint, $method, $data, false);
//            }
//
//            return $response;
//        } catch (Exception $e) {
//            throw new Exception('Помилка запиту: ' . $e->getMessage());
//        }
//    }

    /**
     * @throws Exception
     */
    public function createContact(array $data): array
    {
        $response = $this->sendRequest('contacts', "post", $data);

        return ["success" => $response["code"] !== 3062, "data" => $response];
    }

    /**
     * @throws Exception
     */
    public function storeSaleOrder(array $data)
    {
        $isPurchaseOrder = $data['is_purchase_order'];


        $dataContact["contact_name"] = $data['contact_name'];
        $dataContact["email"] = $data['email'];

        unset($data["contact_name"]);
        unset($data["email"]);
        unset($data["is_purchase_order"]);


//        TODO: Розкоментувати

//        $responseContact = $this->sendRequest('contacts', "post", $dataContact);

        $customerId = 701659000000059005;// $responseContact['contact']['contact_id'];

        $data["customer_id"] = $customerId;

        $response = $this->sendRequest('salesorders', "post", $data);

        if($isPurchaseOrder){

            $purchaseOrderData = $data;
            $purchaseOrderData['vendor_id'] = $this->vendorId;

            $response["purchase_orders"] = $this->sendRequest('purchaseorders', "post", $purchaseOrderData);
        }

        return ["success" => $response["code"] !== 3062, "data" => $response];
    }

    /**
     * @throws Exception
     */
    public function addItem(array $data): array
    {
        return  $this->sendRequest('items', "post", $data);
    }
}
