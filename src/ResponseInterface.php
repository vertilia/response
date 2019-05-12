<?php
declare(strict_types=1);

namespace Vertilia\Response;

/**
 * Children must implement render() method to output response to client.
 */
interface ResponseInterface
{
    /**
     * Output response content
     */
    public function render();
}
