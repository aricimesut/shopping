<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait RequestService
{
    public function performRequest($url, $data = [], $method = "POST")
    {
        //send guzzle request
        $client = new Client(['verify' => false]);
        $requestData = $this->prepareRequestData($data, $method);

        try {
            $response = $client->request($method, config("services.ls_api.base_url") . $url, $requestData);
            $response = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = [];
        }

        return $response;
    }

    /**
     * @param $data
     * @param $method
     * @return array
     */
    protected function prepareRequestData($data, $method): array
    {
        $data["api_key"] = config("services.ls_api.api_key");

        if ($method == 'GET') {
            $requestData['query'] = $data;
        } else {
            $requestData['json'] = $data;
        }

        $requestData['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => config("services.ls_api.api_key")
        ];

        return $requestData;
    }

}
