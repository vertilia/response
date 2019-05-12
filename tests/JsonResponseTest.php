<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;
use Vertilia\ValidArray\ValidArray;

/**
 * @coversDefaultClass JsonResponse
 */
class JsonResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new JsonResponse([]);

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);
        $this->assertInstanceOf(ValidArray::class, $response);

        // check right Content-Type
        $this->assertEquals('application/json', $response->getContentType());
    }

    /**
     * @runInSeparateProcess
     * @dataProvider renderProvider
     * @covers ::render
     * @param array $filter
     * @param string $expected
     */
    public function testRender(array $filter, array $values, string $expected)
    {
        $response = new JsonResponse($filter);

        // fill values
        foreach ($values as $k => $v) {
            $response[$k] = $v;
        }

        // check output
        ob_start();
        $response->render();
        $this->assertEquals($expected, ob_get_clean());
    }

    /** data provider */
    public function renderProvider()
    {
        return [
            [['a' => \FILTER_DEFAULT], ['a' => 'b'], '{"a":"b"}'],
            [['id' => ['filter'=>\FILTER_VALIDATE_INT, 'flags' => \FILTER_FORCE_ARRAY]], ['id' => 15], '{"id":[15]}'],
        ];
    }
}
