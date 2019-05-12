<?php
declare(strict_types=1);

namespace Vertilia\Response;

/**
 * Children must handle HTTP response characteristics, like status code, headers etc.
 */
interface HttpResponseInterface extends ResponseInterface
{
    /**
     * @return int HTTP status code
     */
    public function getStatusCode(): int;

    /**
     * @return string HTTP content type
     */
    public function getContentType(): string;

    /**
     * @return array HTTP response headers
     */
    public function getHeaders(): array;
}
