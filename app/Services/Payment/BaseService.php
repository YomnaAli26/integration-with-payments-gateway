<?php
namespace App\Services\Payment;

use AllowDynamicProperties;
use App\Contracts\PaymentServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

#[AllowDynamicProperties] abstract class BaseService implements PaymentServiceInterface
{
    public function __construct(Client $client,$baseUrl,$headers)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
    }

    public function buildRequest($method, $uri, $body = [])
    {
        try {
            $options = [
                'headers' => $this->headers,
                'verify' => false,
            ];

            if (!empty($body)) {
                $options['form_params'] = $body;
            }

            $response = $this->client->request($method, $this->baseUrl . $uri, $options);

            if ($response->getStatusCode() !== 200) {
                return ['error' => 'Unexpected response status: ' . $response->getStatusCode()];
            }
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return $e->getResponse()
                ? json_decode($e->getResponse()->getBody()->getContents(), true)
                : ['error' => $e->getMessage()];
        }
    }

}
