<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Api\Client;

use Camelot\WhoIcd\Api\SearchQuery;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Icd11
{
    private HttpClientInterface $httpClient;
    private Token $tokenClient;
    private string $apiVersion;
    private string $language;

    public function __construct(HttpClientInterface $httpClient, Token $tokenClient, string $apiVersion = 'v2', string $language = 'en')
    {
        $this->httpClient = $httpClient;
        $this->tokenClient = $tokenClient;
        $this->apiVersion = $apiVersion;
        $this->language = $language;
    }

    /**
     * Returns basic information on the latest release of the ICD-11 Foundation together with the top level Foundation
     * entities.
     *
     * @param null|string $releaseId This is an optional parameter and if ignored, the API will return values from the
     *                               latest released version of the Foundation. If provided, the API will respond using
     *                               that particular release. The values are like "2019-04".
     */
    public function getFoundations(?string $releaseId = null): array
    {
        $options = $releaseId ? ['query' => ['releaseId' => $releaseId]] : [];

        return $this->getClient()->request('GET', '/icd/entity', $options)->toArray();
    }

    /**
     * Provides you information on a specific ICD-11 foundation entity.
     *
     * @param int         $id        Numeric part at the end of the URI for an entity
     * @param null|string $releaseId This is an optional parameter and if ignored, the API will return values from the
     *                               latest released version of the Foundation. If provided, the API will respond using
     *                               that particular release. The values are like "2019-04".
     */
    public function getFoundation(int $id, ?string $releaseId = null): array
    {
        $options = $releaseId ? ['query' => ['releaseId' => $releaseId]] : [];

        return $this->getClient()->request('GET', "/icd/entity/{$id}?", $options)->toArray();
    }

    /** Search the foundation component of the ICD-11. */
    public function searchFoundation(SearchQuery $searchQuery): array
    {
        $options = ['query' => $searchQuery->toArray()];

        return $this->getClient()->request('GET', '/icd/entity/search', $options)->toArray();
    }

    /**
     * Returns basic information on the linearization such as MMS together with the list of available releases.
     *
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics
     */
    public function getLinearization(string $linearizationName): array
    {
        return $this->getClient()->request('GET', "{$linearizationName}")->toArray();
    }

    /**
     * Returns basic information on the released linearization (such as MMS) together with the chapters in it.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     */
    public function getReleaseLinearization(string $releaseId, string $linearizationName): array
    {
        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}")->toArray();
    }

    /**
     * Returns lists the URIs of the entity in the available releases.
     *
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity
     */
    public function getLinearizationById(string $linearizationName, string $id): array
    {
        return $this->getClient()->request('GET', "{$linearizationName}/{$id}")->toArray();
    }

    /**
     * Returns lists the URIs of the residual entity in the available releases.
     *
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity. For residual categories this
     *                                  number is the same as its parent but the residual categories also have other or
     *                                  unspecified at the end.
     * @param string $residual          For residual categories:
     *                                  - this could be 'other' for other specified residual category
     *                                  - or 'unspecified' for the unspecified residual category
     */
    public function getLinearizationByIdResidual(string $linearizationName, string $id, string $residual): array
    {
        return $this->getClient()->request('GET', "{$linearizationName}/{$id}/{$residual}")->toArray();
    }

    /**
     * Return information on a linearization entity.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity
     */
    public function getReleaseLinearizationById(string $releaseId, string $linearizationName, string $id): array
    {
        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}/{$id}")->toArray();
    }

    /**
     * Return information on a residual linearization entity.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity
     * @param string $residual          For residual categories:
     *                                  - this could be 'other' for other specified residual category
     *                                  - or 'unspecified' for the unspecified residual category
     */
    public function getReleaseLinearizationByIdResidual(string $releaseId, string $linearizationName, string $id, string $residual): array
    {
        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}/{$id}/{$residual}")->toArray();
    }

    /**
     * Look up an entity from its code. For postcoordinated code combinations, it provides more information on the code
     * combination such as which axes are used and what values do they have.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $code              The code or code combination that will be looked up. (The & and / characters need to be URL encoded)
     */
    public function getReleaseLinearizationByCode(string $releaseId, string $linearizationName, string $code): array
    {
        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}/codeinfo/{$code}")->toArray();
    }

    /**
     * If the foundation entity is included in the linearization and has a code then that linearization entity is
     * returned. If the foundation entity in included in the linearization but it is a grouping without a code then the
     * system will return the unspecified residual category under that grouping.
     *
     * If the entity is not included in the linearization then the system checks where that entity is aggregated to and
     * then returns that entity.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $foundationUri     The uri of the foundation entity that you'd like to map to this linearization
     */
    public function getReleaseLinearizationLookup(string $releaseId, string $linearizationName = null, string $foundationUri = null): array
    {
        $q = $foundationUri ? '?foundationUri=' . urlencode($foundationUri) : '';

        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}/lookup{$q}")->toArray();
    }

    /**
     * Search the linearization (such as MMS). The search can be customized using the parameters as described Search endpoint.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     */
    public function searchReleaseLinearization(string $releaseId, string $linearizationName, SearchQuery $searchQuery): array
    {
        $options = ['query' => $searchQuery->toArray()];

        return $this->getClient()->request('GET', "{$releaseId}/{$linearizationName}/search", $options)->toArray();
    }

    private function getClient(): HttpClientInterface
    {
        return $this->httpClient->withOptions([
            'base_uri' => 'https://id.who.int/icd/release/11/',
            'headers' => [
                'API-Version' => $this->apiVersion,
                'Accept-Language' => $this->language,
                'Authorization' => "Bearer {$this->getToken()}",
            ],
        ]);
    }

    private function getToken(): string
    {
        return $this->tokenClient->getToken();
    }
}
