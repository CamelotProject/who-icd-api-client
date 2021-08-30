<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Api\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Token
{
    public const TOKEN_ENDPOINT = 'https://icdaccessmanagement.who.int/connect/token';

    public const SCOPE = 'icdapi_access';
    public const GRANT_TYPE = 'client_credentials';

    private HttpClientInterface $httpClient;
    private string $clientId;
    private string $clientSecret;
    private ?string $token = null;

    public function __construct(HttpClientInterface $httpClient, string $clientId, string $clientSecret)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getToken(bool $refresh = false): string
    {
        if (!$refresh && $this->token !== null) {
            return $this->token;
        }
        $data = [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => self::SCOPE,
                'grant_type' => self::GRANT_TYPE,
            ],
        ];
        $response = $this->httpClient->request('POST', self::TOKEN_ENDPOINT, $data);
        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException("Fetching token failed: {$response->getContent(false)}");
        }
        $content = $response->toArray();
        $this->token = $content['access_token'];

        return $this->token;
    }
}
