<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Api\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Icd10
{
    private HttpClientInterface $httpClient;
    private Token $token;

    public function __construct(HttpClientInterface $httpClient, Token $token)
    {
        $this->httpClient = $httpClient;
        $this->token = $token;
    }

    /** Lists the available ICD-10 releases. */
    public function getReleases(): array
    {
        return $this->getClient()->request('GET', '')->toArray();
    }

    /**
     * Returns basic information on the released version of ICD-10 together with the chapters in it.
     *
     * @param string $release The id for the release. For ICD-10, this is generally the year e.g. 2016.
     */
    public function getRelease(string $release): array
    {
        return $this->getClient()->request('GET', $release)->toArray();
    }

    /**
     * Lists the available ICD-10 releases for the requested category.
     *
     * @param string $code ICD-10 category code. For blocks the code range.
     */
    public function getCode(string $code): array
    {
        return $this->getClient()->request('GET', $code)->toArray();
    }

    /**
     * Returns information on the category together with its children categories.
     *
     * @param string $code    ICD-10 category code. For blocks the code range.
     * @param string $release The id for the release. For ICD-10, this is generally the year e.g. 2016.
     */
    public function getCodeByRelease(string $code, string $release): array
    {
        return $this->getClient()->request('GET', "{$release}/{$code}")->toArray();
    }

    private function getClient(): HttpClientInterface
    {
        return $this->httpClient->withOptions([
            'base_uri' => 'https://id.who.int/icd/release/10/',
            'headers' => [
                'API-Version' => 'v2',
                'Accept-Language' => 'en',
                'Authorization' => "Bearer {$this->getToken()}",
            ],
        ]);
    }

    private function getToken(): string
    {
        return $this->token->getToken();
    }
}
