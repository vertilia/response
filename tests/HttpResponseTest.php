<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;
use Vertilia\ValidArray\ValidArray;

/**
 * @coversDefaultClass HttpResponse
 */
class HttpResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new HttpResponse([], 'plain/unknown');

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);
        $this->assertInstanceOf(ValidArray::class, $response);

        // check right Content-Type
        $this->assertEquals('plain/unknown', $response->getContentType());
    }

    /**
     * @dataProvider httpResponseProvider
     * @covers ::setStatusCode
     * @covers ::setContentType
     * @covers ::setHeader
     * @covers ::getStatusCode
     * @covers ::getContentType
     * @covers ::getHeaders
     * @param int $status_code
     * @param string $content_type
     * @param array $headers
     */
    public function testHttpResponse($status_code, $content_type, $headers)
    {
        $response = (new HttpResponse([]))->setStatusCode($status_code)->setContentType($content_type);
        if (\is_array($headers)) {
            foreach ($headers as $name => $value) {
                $response->setHeader($name, $value);
            }
        }
        $this->assertEquals($status_code, $response->getStatusCode());
        $this->assertEquals($content_type, $response->getContentType());
        $this->assertEquals(\array_change_key_case($headers, \CASE_LOWER), $response->getHeaders());
    }

    /** data provider */
    public function httpResponseProvider()
    {
        return [
            [200, 'plain/html', ['Connection' => 'close']],
            [200, 'plain/html', ['Content-Type' => 'unknown']],
        ];
    }

    /**
     * @dataProvider setHeaderProvider
     * @covers ::setHeader
     * @covers ::getHeaders
     * @param array $headers
     * @param string $name
     * @param string $value
     * @param bool $multiple
     * @param mixed $expected_headers
     */
    public function testSetHeader(array $headers, string $name, string $value, bool $multiple, array $expected_headers)
    {
        $response = new HttpResponse([]);
        foreach ($headers as $h_name => $h_value) {
            $response->setHeader($h_name, $h_value);
        }
        $this->assertEquals($expected_headers, $response->setHeader($name, $value, $multiple)->getHeaders());
    }

    /** data provider */
    public function setHeaderProvider()
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
     * @dataProvider preRenderProvider
     * @covers ::setHeader
     * @covers ::preRender
     * @param string $content_type
     * @param array $headers
     * @param array $expected_headers
     */
    public function testPreRender($content_type, array $headers, array $expected_headers)
    {
        $response = new HttpResponse([], $content_type);
        foreach ($headers as $h_name => $h_value) {
            if (\is_array($h_value)) {
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
            $this->assertEquals($content_type, $response->getHeaders()['content-type']);
        }

        // best effort code
        // will not work since headers not registered in CLI environment and
        // headers_list() will always return empty array
//        $this->assertEquals($expected_headers, \headers_list());
        $this->assertEquals([], \headers_list());
    }

    /** data provider */
    public function preRenderProvider()
    {
        return [
            [null, ['connection' => 'My Value'], ['connection: My Value']],
            ['text/plain', ['connection' => 'close', 'my-header' => 'My Value'], ['connection: close', 'my-header: My Value']],
            ['application/json', ['connection' => ['close', 'My Value']], ['connection: close', 'connection: My Value']],
        ];
    }
}
