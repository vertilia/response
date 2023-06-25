<?php

namespace Vertilia\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass JsonResponse
 */
class JsonResponseTest extends TestCase
{
    /**
     * @covers JsonResponse::__construct
     * @covers JsonResponse::getContentType
     */
    public function testHttpResponseConstruct()
    {
        $response = new JsonResponse([]);

        // check inheritances
        $this->assertInstanceOf(HttpResponseInterface::class, $response);

        // check right Content-Type
        $this->assertEquals('application/json', $response->getContentType());
    }

    /**
     * @runInSeparateProcess
     * @dataProvider providerRender
     * @covers JsonResponse::render
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

    public static function providerRender(): array
    {
        return [
            [['a' => FILTER_DEFAULT], ['a' => 'b'], '{"a":"b"}'],
            [['id' => ['filter'=>FILTER_VALIDATE_INT, 'flags' => FILTER_FORCE_ARRAY]], ['id' => 15], '{"id":[15]}'],
        ];
    }
}
