<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Api;

use Stringable;
use function get_object_vars;
use function ltrim;
use function urlencode;

final class SearchQuery implements Stringable
{
    /** Text to be searched. Having the character % at the end will be regarded as a wild card for that word. */
    private ?string $q;
    /**
     * Comma separated list of URIs. If provided, the search will be performed on the entities provided and their
     * descendants.
     */
    private ?string $subtreesFilter = null;
    /**
     * Comma or semicolon separated list of chapter codes eg:01;02;21 When provided, the search will be performed only
     * on these chapters.
     */
    private ?string $chapterFilter = null;
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
    private bool $useFlexisearch = false;
    /**
     * If set to true the search result entities are provided in a nested data structure representing the ICD-11
     * hierarchy. Otherwise they are listed as flat list of matches.
     */
    private bool $flatResults = true;
    /** When not provided, the search is performed on the title, synonym and narrowerTerm properties of the entity. */
    private ?string $propertiesToBeSearched = null;
    /**
     * If ignored, the API will return values from the latest released version of the Foundation. If provided, the API
     * will respond using that particular release. The values are like "2019-04".
     */
    private ?string $releaseId = null;
    /**
     * If set to false the search result highlighting is turned off and the results don't contain special tags for
     * highlighting where the results are found within the text.
     */
    private bool $highlightingEnabled = false;

    public function __construct(string $searchText)
    {
        $this->q = $searchText;
    }

    public function __toString(): string
    {
        $s = '';
        foreach ($this->toArray() as $p => $v) {
            $v = \is_bool($v) ? ($v ? 'true' : 'false') : (\is_string($v) ? urlencode($v) : $v);
            $s .= "&{$p}={$v}";
        }

        return ltrim($s, '&');
    }

    public static function create(string $searchText): self
    {
        return new self($searchText);
    }

    public function toArray(): array
    {
        $a = [];
        foreach (get_object_vars($this) as $p => $v) {
            if ($v === null) {
                continue;
            }
            $a[$p] = \is_bool($v) ? ($v ? 'true' : 'false') : $v;
        }

        return $a;
    }

    public function getSearchText(): ?string
    {
        return $this->q;
    }

    public function setSearchText(string $searchText): self
    {
        $this->q = $searchText;

        return $this;
    }

    public function getSubtreesFilter(): ?string
    {
        return $this->subtreesFilter;
    }

    public function setSubtreesFilter(?string $subtreesFilter): self
    {
        $this->subtreesFilter = urlencode($subtreesFilter);

        return $this;
    }

    public function getChapterFilter(): ?string
    {
        return $this->chapterFilter;
    }

    public function setChapterFilter(?string $chapterFilter): self
    {
        $this->chapterFilter = $chapterFilter;

        return $this;
    }

    public function isUseFlexisearch(): bool
    {
        return $this->useFlexisearch;
    }

    public function setUseFlexisearch(bool $useFlexisearch): self
    {
        $this->useFlexisearch = $useFlexisearch;

        return $this;
    }

    public function isFlatResults(): bool
    {
        return $this->flatResults;
    }

    public function setFlatResults(bool $flatResults): self
    {
        $this->flatResults = $flatResults;

        return $this;
    }

    public function getPropertiesToBeSearched(): ?string
    {
        return $this->propertiesToBeSearched;
    }

    public function setPropertiesToBeSearched(?string $propertiesToBeSearched): self
    {
        $this->propertiesToBeSearched = $propertiesToBeSearched;

        return $this;
    }

    public function getReleaseId(): ?string
    {
        return $this->releaseId;
    }

    public function setReleaseId(?string $releaseId): self
    {
        $this->releaseId = $releaseId;

        return $this;
    }

    public function isHighlightingEnabled(): bool
    {
        return $this->highlightingEnabled;
    }

    public function setHighlightingEnabled(bool $highlightingEnabled): self
    {
        $this->highlightingEnabled = $highlightingEnabled;

        return $this;
    }
}
