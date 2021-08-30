<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Tests\Api\Client;

use Camelot\WhoIcd\Api\Client\Icd10;
use Camelot\WhoIcd\Api\Client\Token;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @covers \Camelot\WhoIcd\Api\Client\Icd10
 *
 * @internal
 */
final class Icd10Test extends TestCase
{
    public function testGetReleases(): void
    {
        $client = new Icd10(HttpClient::create(), $this->getTokenClient());
        $expected = [
            'http://id.who.int/icd/release/10/2019',
            'http://id.who.int/icd/release/10/2016',
            'http://id.who.int/icd/release/10/2010',
            'http://id.who.int/icd/release/10/2008',
        ];

        static::assertSame($expected, $client->getReleases()['release']);
    }

    public function testGetRelease(): void
    {
        $client = new Icd10(HttpClient::create(), $this->getTokenClient());
        $expected = [
            'http://id.who.int/icd/release/10/2019/I',
            'http://id.who.int/icd/release/10/2019/II',
            'http://id.who.int/icd/release/10/2019/III',
            'http://id.who.int/icd/release/10/2019/IV',
            'http://id.who.int/icd/release/10/2019/V',
            'http://id.who.int/icd/release/10/2019/VI',
            'http://id.who.int/icd/release/10/2019/VII',
            'http://id.who.int/icd/release/10/2019/VIII',
            'http://id.who.int/icd/release/10/2019/IX',
            'http://id.who.int/icd/release/10/2019/X',
            'http://id.who.int/icd/release/10/2019/XI',
            'http://id.who.int/icd/release/10/2019/XII',
            'http://id.who.int/icd/release/10/2019/XIII',
            'http://id.who.int/icd/release/10/2019/XIV',
            'http://id.who.int/icd/release/10/2019/XV',
            'http://id.who.int/icd/release/10/2019/XVI',
            'http://id.who.int/icd/release/10/2019/XVII',
            'http://id.who.int/icd/release/10/2019/XVIII',
            'http://id.who.int/icd/release/10/2019/XIX',
            'http://id.who.int/icd/release/10/2019/XX',
            'http://id.who.int/icd/release/10/2019/XXI',
            'http://id.who.int/icd/release/10/2019/XXII',
        ];

        static::assertSame($expected, $client->getRelease('2019')['child']);
    }

    public function testGetCode(): void
    {
        $client = new Icd10(HttpClient::create(), $this->getTokenClient());
        $expected = 'Cholera due to Vibrio cholerae 01, biovar cholerae';

        static::assertSame($expected, $client->getCode('A00.0')['title']['@value']);
    }

    public function testGetCodeByRelease(): void
    {
        $client = new Icd10(HttpClient::create(), $this->getTokenClient());
        $expected = 'Cholera due to Vibrio cholerae 01, biovar cholerae';

        static::assertSame($expected, $client->getCodeByRelease('A00.0', '2019')['title']['@value']);
    }

    private function getTokenClient(): Token
    {
        $client = HttpClient::create();

        return new Token($client, $_ENV['WHO_CLIENT_ID'], $_ENV['WHO_CLIENT_SECRET']);
    }
}
