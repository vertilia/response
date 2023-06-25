<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;
use Vertilia\ValidArray\ValidArray;

/**
 * @coversDefaultClass Response
 */
class ResponseTest extends TestCase
{
    /**
     * @covers Response::__construct
     * @covers Response::render
     */
    public function testResponse()
    {
        $response = new Response(['id' => FILTER_VALIDATE_INT]);

        // check inheritances
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(ValidArray::class, $response);

        $response['id'] = 42;

        ob_start();
        $response->render();
        $output = ob_get_clean();

        $a = unserialize($output);
        $this->assertIsArray($a);
        $this->assertCount(1, $a);
        $this->assertArrayHasKey('id', $a);
        $this->assertSame(42, $a['id']);
    }
}
