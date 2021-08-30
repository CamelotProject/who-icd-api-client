<?php

declare(strict_types=1);

namespace Camelot\WhoIcd\Tests\Api\Client;

use Camelot\WhoIcd\Api\Client\Token;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use function json_encode;

/**
 * @covers \Camelot\WhoIcd\Api\Client\Token
 *
 * @internal
 */
final class TokenTest extends TestCase
{
    public function testGetToken(): void
    {
        $client = HttpClient::create();
        $token = new Token($client, $_ENV['WHO_CLIENT_ID'], $_ENV['WHO_CLIENT_SECRET']);

        static::assertNotEmpty($token->getToken());
    }

    public function testGetTokenRepeatIsCached(): void
    {
        $client = new MockHttpClient(new MockResponse(json_encode(['access_token' => 'abc123'])));
        $token = new Token($client, $_ENV['WHO_CLIENT_ID'], $_ENV['WHO_CLIENT_SECRET']);

        static::assertNotEmpty($token->getToken());
        static::assertNotEmpty($token->getToken());
    }

    public function testGetTokenRequestThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Fetching token failed: {"error":"invalid_client"}');

        $client = HttpClient::create();
        $token = new Token($client, '', '');
        $token->getToken();
    }
}
