WHO ICD API Client
==================

This library provides a PHP client interface to the World Health Organisation's
International Classification of Diseases (ICD) API.

Requirements
------------

**NOTE:** You **must** first need to generate an [ICD API access key][icdapi] to 
use the library.

* PHP >= 7.4
* Composer >= 1.6

Installation
------------

```shell
composer require camelot/who-icd-api-client
```

Configuration
-------------

### Symfony Framework

Add your ICD API access key to `.env.local`

```shell
WHO_CLIENT_ID="your client ID"
WHO_CLIENT_SECRET="your secret"
```

Add the following to `config/services.yaml`

```yaml
    Camelot\WhoIcd\Api\Client\Token:
        arguments:
            $clientId: '%env(WHO_CLIENT_ID)%'
            $clientSecret: '%env(WHO_CLIENT_SECRET)%'

    Camelot\WhoIcd\Api\Client\Icd11:
    Camelot\WhoIcd\Api\Client\Icd10:
```

Usage
-----

Also see WHO's [ICD API Swagger][swagger] documentation page.

### ICD-11
#### Methods

```
    /**
     * Returns basic information on the latest release of the ICD-11 Foundation together with the top level Foundation
     * entities.
     *
     * @param null|string $releaseId This is an optional parameter and if ignored, the API will return values from the
     *                               latest released version of the Foundation. If provided, the API will respond using
     *                               that particular release. The values are like "2019-04".
     */
    public function getFoundations(?string $releaseId = null): array

    /**
     * Provides you information on a specific ICD-11 foundation entity.
     *
     * @param int         $id        Numeric part at the end of the URI for an entity
     * @param null|string $releaseId This is an optional parameter and if ignored, the API will return values from the
     *                               latest released version of the Foundation. If provided, the API will respond using
     *                               that particular release. The values are like "2019-04".
     */
    public function getFoundation(int $id, ?string $releaseId = null): array

    /** Search the foundation component of the ICD-11. */
    public function searchFoundation(SearchQuery $searchQuery): array

    /**
     * Returns basic information on the linearization such as MMS together with the list of available releases.
     *
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics
     */
    public function getLinearization(string $linearizationName): array

    /**
     * Returns basic information on the released linearization (such as MMS) together with the chapters in it.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     */
    public function getReleaseLinearization(string $releaseId, string $linearizationName): array

    /**
     * Returns lists the URIs of the entity in the available releases.
     *
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity
     */
    public function getLinearizationById(string $linearizationName, string $id): array

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

    /**
     * Return information on a linearization entity.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $id                Numeric part at the end of the URI for an entity
     */
    public function getReleaseLinearizationById(string $releaseId, string $linearizationName, string $id): array

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

    /**
     * Look up an entity from its code. For postcoordinated code combinations, it provides more information on the code
     * combination such as which axes are used and what values do they have.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     * @param string $code              The code or code combination that will be looked up. (The & and / characters need to be URL encoded)
     */
    public function getReleaseLinearizationByCode(string $releaseId, string $linearizationName, string $code): array

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

    /**
     * Search the linearization (such as MMS). The search can be customized using the parameters as described Search endpoint.
     *
     * @param string $releaseId         The id for the release. This is generally formatted like this: 2019-04
     * @param string $linearizationName Short name for the linearization. e.g. mms for ICD Mortality and Morbidity Statistics.
     */
    public function searchReleaseLinearization(string $releaseId, string $linearizationName, SearchQuery $searchQuery): array
```

### ICD-10
#### Methods

```
    /** Lists the available ICD-10 releases. */
    public function getReleases(): array

    /**
     * Returns basic information on the released version of ICD-10 together with the chapters in it.
     *
     * @param string $release The id for the release. For ICD-10, this is generally the year e.g. 2016.
     */
    public function getRelease(string $release): array

    /**
     * Lists the available ICD-10 releases for the requested category.
     *
     * @param string $code ICD-10 category code. For blocks the code range.
     */
    public function getCode(string $code): array

    /**
     * Returns information on the category together with its children categories.
     *
     * @param string $code    ICD-10 category code. For blocks the code range.
     * @param string $release The id for the release. For ICD-10, this is generally the year e.g. 2016.
     */
    public function getCodeByRelease(string $code, string $release): array
```

### Search Query

The SearchQuery object allows you to configure the query parameters for searches.

The search text is the only required parameter, e.g.

```
$query = SearchQuery::create('my search text');
```

#### Methods

```
    /** Text to be searched. Having the character % at the end will be regarded as a wild card for that word. */
    public function setSearchText(string $searchText): self

    /**
     * Comma separated list of URIs. If provided, the search will be performed on the entities provided and their
     * descendants.
     */
    public function setSubtreesFilter(?string $subtreesFilter): self

    /**
     * Comma or semicolon separated list of chapter codes eg:01;02;21 When provided, the search will be performed only
     * on these chapters.
     */
    public function setChapterFilter(?string $chapterFilter): self

    /**
     * Changes the search mode to flexible search.
     *
     * - In the regular search mode, the Coding Tool will only give you results that contain all of the words that
     *   you've used in your search. It accepts different variants or synonyms of the words but essentially it searches
     *   for a result that contains all components of your search. Whereas in flexible search mode, the results do not
     *   have to contain all of the words that are typed. It would still try to find the best matching phrase but there
     *   may be words in your search that are not matched at all
     * - It is recommended to use flexible search only when regular search does not provide a result.
     */
    public function setUseFlexisearch(bool $useFlexisearch): self

    /**
     * If set to true the search result entities are provided in a nested data structure representing the ICD-11
     * hierarchy. Otherwise they are listed as flat list of matches.
     */
    public function setFlatResults(bool $flatResults): self

    /** When not provided, the search is performed on the title, synonym and narrowerTerm properties of the entity. */
    public function setPropertiesToBeSearched(?string $propertiesToBeSearched): self

    /**
     * If ignored, the API will return values from the latest released version of the Foundation. If provided, the API
     * will respond using that particular release. The values are like "2019-04".
     */
    public function setReleaseId(?string $releaseId): self

    /**
     * If set to false the search result highlighting is turned off and the results don't contain special tags for
     * highlighting where the results are found within the text.
     */
    public function setHighlightingEnabled(bool $highlightingEnabled): self
```

[icdapi]: https://icd.who.int/icdapi
[swagger]: https://id.who.int/swagger/index.html
