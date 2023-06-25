<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass HttpResponse
 */
class HttpResponseTest extends TestCase
{
    /**
     * @covers HttpResponse::__construct
     * @covers HttpResponse::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new HttpResponse([], 'plain/unknown');

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);

        // check right Content-Type
        $this->assertSame('plain/unknown', $response->getContentType());
    }

    /**
     * @dataProvider providerHttpResponse
     * @covers HttpResponse::setStatusCode
     * @covers HttpResponse::setContentType
     * @covers HttpResponse::setHeader
     * @covers HttpResponse::getStatusCode
     * @covers HttpResponse::getContentType
     * @covers HttpResponse::getHeaders
     */
    public function testHttpResponse(int $status_code, string $content_type, array $headers)
    {
        $response = (new HttpResponse([]))->setStatusCode($status_code)->setContentType($content_type);
        foreach ($headers as $name => $value) {
            $response->setHeader($name, $value);
        }
        $this->assertSame($status_code, $response->getStatusCode());
        $this->assertSame($content_type, $response->getContentType());
        $this->assertSame(array_change_key_case($headers, CASE_LOWER), $response->getHeaders());
    }

    public static function providerHttpResponse(): array
    {
        return [
            [200, 'plain/html', ['Connection' => 'close']],
            [200, 'plain/html', ['Content-Type' => 'unknown']],
        ];
    }

    /**
     * @dataProvider providerSetHeader
     * @covers HttpResponse::setHeader
     * @covers HttpResponse::getHeaders
     */
    public function testSetHeader(array $headers, string $name, string $value, bool $multiple, array $expected_headers)
    {
        $response = new HttpResponse([]);
        foreach ($headers as $h_name => $h_value) {
            $response->setHeader($h_name, $h_value);
        }
        $this->assertSame($expected_headers, $response->setHeader($name, $value, $multiple)->getHeaders());
    }

    public static function providerSetHeader(): array
    {
        return [
            [['Connection' => 'close'], 'My-Header', 'My Value', false, ['connection' => 'close', 'my-header' => 'My Value']],
            [['Connection' => 'close'], 'My-Header', 'My Value', true, ['connection' => 'close', 'my-header' => 'My Value']],
            [['Connection' => 'close'], 'Connection', 'My Value', false, ['connection' => 'My Value']],
            [['Connection' => 'close'], 'Connection', 'My Value', true, ['connection' => ['close', 'My Value']]],
        ];
    }

    /**
     * @runInSeparateProcess
     * @dataProvider providerPreRender
     * @covers HttpResponse::setHeader
     * @covers HttpResponse::preRender
     */
    public function testPreRender(?string $content_type, array $headers, array $expected_headers)
    {
        $response = new HttpResponse([], $content_type);
        foreach ($headers as $h_name => $h_value) {
            if (is_array($h_value)) {
                foreach ($h_value as $v) {
                    $response->setHeader($h_name, $v, true);
                }
            } else {
                $response->setHeader($h_name, $h_value);
            }
        }

        $response->preRender();

        // find $content_type in headers
        if ($content_type) {
            $this->assertArrayHasKey('content-type', $response->getHeaders());
            $this->assertSame($content_type, $response->getHeaders()['content-type']);
        }

        // best effort code
        // will not work since headers not registered in CLI environment and
        // headers_list() will always return empty array
//        $this->assertSame($expected_headers, headers_list());
        $this->assertSame([], headers_list());
    }

    public static function providerPreRender(): array
    {
        return [
            [null, ['connection' => 'My Value'], ['connection: My Value']],
            ['text/plain', ['connection' => 'close', 'my-header' => 'My Value'], ['connection: close', 'my-header: My Value']],
            ['application/json', ['connection' => ['close', 'My Value']], ['connection: close', 'connection: My Value']],
        ];
    }
}
