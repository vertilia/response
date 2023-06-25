<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass HtmlResponse
 */
class HtmlResponseTest extends TestCase
{
    /**
     * @covers HttpResponse::__construct
     * @covers HttpResponse::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new HtmlResponse([]);

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);

        // check right Content-Type
        $this->assertSame('text/html', $response->getContentType());
    }

    /**
     * @runInSeparateProcess
     * @covers HttpResponse::render
     */
    public function testRender()
    {
        $response = new HtmlResponse([]);

        // simple line
        $string = 'simple line of output';
        $response->content = $string;

        // check output
        ob_start();
        $response->render();
        $this->assertSame($string, ob_get_clean());

        // file handler
        $string = "buffer test string\n";
        $buffer = fopen('php://memory', 'r+');
        fputs($buffer, $string);
        $response->content = $buffer;

        // check output
        ob_start();
        $response->render();
        $this->assertSame($string, ob_get_clean());

        // file handler
        $string = 'callback test string';
        $response->content = function () use ($string) {echo $string;};

        // check output
        ob_start();
        $response->render();
        $this->assertSame($string, ob_get_clean());
    }
}
