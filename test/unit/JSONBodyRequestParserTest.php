<?php
declare(strict_types=1);

namespace test\unit;

use GuzzleHttp\Psr7\ServerRequest;
use Ingenerator\ApiEmulator\JSONRequestBodyParser;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class JSONBodyRequestParserTest extends TestCase
{

    #[TestWith([[], 'no content type header'])]
    #[TestWith([['Content-Type' => 'application/x-www-form-urlencoded']])]
    public function test_it_returns_same_request_if_not_json($headers): void
    {
        $request = new ServerRequest(
            'GET',
            '/any/uri',
            $headers,
            '{"it looks like":"json but it is not"}'
        );

        $this->assertSame($request, $this->newSubject()->parse($request));
    }

    #[TestWith(['application/json'])]
    #[TestWith(['application/json; charset=utf8'])]
    #[TestWith(['application/json;charset=utf8'])]
    public function test_it_returns_request_with_decoded_json_body(string $content_type): void
    {
        $request = new ServerRequest(
            'GET',
            '/any/uri',
            ['Content-Type' => $content_type],
            '{"I am": "a json body"}',
        );

        $new_request = $this->newSubject()->parse($request);
        $this->assertNotSame($request, $new_request, 'Should have returned a new request');
        $this->assertSame(['I am' => 'a json body'], $new_request->getParsedBody());
    }

    public function test_the_raw_content_is_still_readable_after_parsing(): void
    {
        $original_body = '["any", "json", "here"]';
        $parsed = $this->newSubject()->parse(
            new ServerRequest(
                'GET',
                '/any/uri',
                ['Content-Type' => 'application/json'],
                $original_body,
            )
        );

        $this->assertSame(['any', 'json', 'here'], $parsed->getParsedBody());
        $this->assertSame($original_body, $parsed->getBody()->getContents());
    }

    public function test_it_throws_if_json_request_already_has_parsed_body(): void
    {
        // Shouldn't be possible at the PHP layer, but just in case guzzle behaviour changes in future
        $request = (new ServerRequest(
            'GET',
            '/any/uri',
            ['Content-Type' => 'application/json'],
            '["collision"]',
        ))->withParsedBody(['post' => 'stuff']);
        $subject = $this->newSubject();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('parsed body');
        $subject->parse($request);
    }

    private function newSubject(): JSONRequestBodyParser
    {
        return new JSONRequestBodyParser();
    }
}
