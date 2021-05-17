<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;
use Vertilia\ValidArray\ValidArray;

/**
 * @coversDefaultClass HtmlResponse
 */
class HtmlResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new HtmlResponse([]);

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);
        $this->assertInstanceOf(ValidArray::class, $response);

        // check right Content-Type
        $this->assertEquals('text/html', $response->getContentType());
    }

    /**
     * @runInSeparateProcess
     * @covers ::render
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
        $this->assertEquals($string, ob_get_clean());

        // file handler
        $string = "buffer test string\n";
        $buffer = fopen('php://memory', 'r+');
        fputs($buffer, $string);
        $response->content = $buffer;

        // check output
        ob_start();
        $response->render();
        $this->assertEquals($string, ob_get_clean());

        // file handler
        $string = 'callback test string';
        $response->content = function () use ($string) {echo $string;};

        // check output
        ob_start();
        $response->render();
        $this->assertEquals($string, ob_get_clean());
    }
}
