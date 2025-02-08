<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

trait InventoryRequest
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $refreshToken;
    protected string $accessToken;

    /**
     * @throws Exception
     */
    private function refreshToken(): void
    {
        $refreshToken = $this->refreshToken;
        $clientId = $this->clientId;
        $clientSecret = $this->clientSecret;

        $response = Http::withoutVerifying() // ахутенг SSL
        ->post("https://accounts.zoho.eu/oauth/v2/token?refresh_token=$refreshToken&client_id=$clientId&client_secret=$clientSecret&grant_type=refresh_token")->json();

        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];

            cache()->put('zoho_access_token', $this->accessToken, 3660);
        } else {
            throw new Exception('Не вдалося оновити токен: ' . json_encode($response));
        }
    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $endpoint, string $method, array $data = [], array $param = [], bool $retry = true): array
    {
        $this->accessToken = cache('zoho_access_token') ?? $this->accessToken;

        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken,
        ];
        try {
            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->withQueryParameters($param)
                ->$method("https://www.zohoapis.eu/inventory/v1/$endpoint", $data)
                ->json();

            if (isset($response['code']) && $response['code'] === 57 && $retry) {
                $this->refreshToken();

                return $this->sendRequest($endpoint, $method, $data, [], false);
            }

            return $response;
        } catch (Exception $e) {
            throw new Exception('Помилка запиту: ' . $e->getMessage());
        }
    }
}
