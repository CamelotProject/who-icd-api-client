<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Tests\Api\Client;

use Camelot\WhoIcd\Api\Client\Icd11;
use Camelot\WhoIcd\Api\Client\Token;
use Camelot\WhoIcd\Api\SearchQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @covers \Camelot\WhoIcd\Api\Client\Icd11
 *
 * @internal
 */
final class Icd11Test extends TestCase
{
    public function providerFoundations(): iterable
    {
        yield 'Null' => ['2021-05', null];
        yield 'Empty string' => ['2021-05', ''];
        yield '2019-04' => ['2019-04', '2019-04'];
        yield '2021-05' => ['2021-05', '2021-05'];
    }

    /** @dataProvider providerFoundations */
    public function testGetFoundations(string $expected, ?string $releaseId): void
    {
        $result = $this->getClient()->getFoundations($releaseId);

        static::assertSame($expected, $result['releaseId']);
    }

    public function providerFoundation(): iterable
    {
        yield [
            'Autism spectrum disorder without disorder of intellectual development and with mild or no impairment of functional language',
            120443468,
            null,
        ];
        yield [
            'Autism spectrum disorder without disorder of intellectual development and with mild or no impairment of functional language',
            120443468,
            '2019-04',
        ];
    }

    /** @dataProvider providerFoundation */
    public function testGetFoundation(string $expected, int $id, ?string $releaseId): void
    {
        $result = $this->getClient()->getFoundation($id, $releaseId);

        static::assertSame($expected, $result['title']['@value']);
    }

    public function providerSearchFoundation(): iterable
    {
        yield ['Attention deficit hyperactivity disorder', SearchQuery::create('adhd')->setHighlightingEnabled(false)];
    }

    /** @dataProvider providerSearchFoundation */
    public function testSearchFoundation(string $expected, SearchQuery $query): void
    {
        $result = $this->getClient()->searchFoundation($query);

        static::assertNotCount(0, $result['destinationEntities']);
        $this->addToAssertionCount(1);
        foreach ($result['destinationEntities'] as $entity) {
            if ($entity['title'] === $expected) {
                return;
            }
        }
        static::fail("Did not find expected entity with title '{$expected}'");
    }

    public function providerLinearization(): iterable
    {
        yield ['International Classification of Diseases 11th Revision - ICD-11 for Mortality and Morbidity Statistics', 'mms'];
    }

    /** @dataProvider providerLinearization */
    public function testGetLinearization(string $expected, string $linearization): void
    {
        $result = $this->getClient()->getLinearization($linearization);

        static::assertSame($expected, $result['title']['@value']);
    }

    public function providerReleaseLinearization(): iterable
    {
        yield ['International Classification of Diseases 11th Revision - Mortality and Morbidity Statistics', '2019-04', 'mms'];
    }

    /** @dataProvider providerReleaseLinearization */
    public function testGetReleaseLinearization(string $expected, string $releaseId, string $linearizationName): void
    {
        $result = $this->getClient()->getReleaseLinearization($releaseId, $linearizationName);

        static::assertSame($expected, $result['title']['@value']);
        static::assertSame($releaseId, $result['releaseId']);
    }

    public function testGetLinearizationById(): void
    {
        $result = $this->getClient()->getLinearizationById('mms', '21500692');

        static::assertSame('Endocrine, nutritional or metabolic diseases', $result['title']['@value']);
    }

    public function testGetReleaseLinearizationByIdResidual(): void
    {
        $result = $this->getClient()->getReleaseLinearizationByIdResidual('2019-04', 'mms', '135352227', 'other');
        $expected = 'Other specified bacterial intestinal infections';

        static::assertSame($expected, $result['title']['@value']);
    }

    public function testGetReleaseLinearizationById(): void
    {
        $result = $this->getClient()->getReleaseLinearizationById('2021-05', 'mms', '21500692');
        $expected = 'Endocrine, nutritional or metabolic diseases';

        static::assertSame($expected, $result['title']['@value']);
    }

    public function testGetLinearizationByIdResidual(): void
    {
        $result = $this->getClient()->getLinearizationByIdResidual('mms', '135352227', 'other');
        $expected = 'Other specified bacterial intestinal infections';

        static::assertSame($expected, $result['title']['@value']);
    }

    public function testGetReleaseLinearizationByCode(): void
    {
        $result = $this->getClient()->getReleaseLinearizationByCode('2021-05', 'mms', '02');
        $expected = 'http://id.who.int/icd/release/11/2021-05/mms/codeinfo/02';

        static::assertSame($expected, $result['@id']);
    }

    public function testGetReleaseLinearizationLookup(): void
    {
        static::markTestIncomplete();

        $result = $this->getClient()->getReleaseLinearizationLookup('2021-05');
        $expected = '';

        static::assertSame($expected, $result);
    }

    public function testSearchReleaseLinearization(): void
    {
        $result = $this->getClient()->searchReleaseLinearization('2019-04', 'mms', SearchQuery::create('autism%')->setHighlightingEnabled(false));
        $expected = 'Autism spectrum disorder, unspecified';

        static::assertNotCount(0, $result['destinationEntities']);

        $this->addToAssertionCount(1);
        foreach ($result['destinationEntities'] as $entity) {
            if ($entity['title'] === $expected) {
                return;
            }
        }
        static::fail("Did not find destinationEntities value with title: '{$expected}'");
    }

    private function getClient(): Icd11
    {
        return new Icd11(HttpClient::create(), $this->getTokenClient(), 'v2', 'en');
    }

    private function getTokenClient(): Token
    {
        $client = HttpClient::create();

        return new Token($client, $_ENV['WHO_CLIENT_ID'], $_ENV['WHO_CLIENT_SECRET']);
    }
}
