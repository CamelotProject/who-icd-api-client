<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Tests\Api;

use Camelot\WhoIcd\Api\SearchQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Camelot\WhoIcd\Api\SearchQuery
 *
 * @internal
 */
final class SearchQueryTest extends TestCase
{
    public function providerSearchQuery(): iterable
    {
        yield 'default' => [
            SearchQuery::create('query')
                ->setSubtreesFilter('/icd/entity/334423054')
                ->setChapterFilter('06')
                ->setUseFlexisearch(true)
                ->setFlatResults(false)
                ->setPropertiesToBeSearched('pockets')
                ->setReleaseId('2019-04')
                ->setHighlightingEnabled(true),
        ];
    }

    /** @dataProvider providerSearchQuery */
    public function testToArray(SearchQuery $searchQuery): void
    {
        $expected = [
            'q' => 'query',
            'subtreesFilter' => '%2Ficd%2Fentity%2F334423054',
            'chapterFilter' => '06',
            'useFlexisearch' => 'true',
            'flatResults' => 'false',
            'propertiesToBeSearched' => 'pockets',
            'releaseId' => '2019-04',
            'highlightingEnabled' => 'true',
        ];
        static::assertSame($expected, $searchQuery->toArray());
    }

    /** @dataProvider providerSearchQuery */
    public function testToString(SearchQuery $searchQuery): void
    {
        $expected = 'q=query' .
            '&subtreesFilter=%252Ficd%252Fentity%252F334423054' .
            '&chapterFilter=06' .
            '&useFlexisearch=true' .
            '&flatResults=false' .
            '&propertiesToBeSearched=pockets' .
            '&releaseId=2019-04' .
            '&highlightingEnabled=true';
        static::assertSame($expected, (string) $searchQuery);
    }
}
