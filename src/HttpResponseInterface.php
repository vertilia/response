<?php

namespace Vertilia\Response;

/**
 * Children must handle HTTP response characteristics, like status code, headers etc.
 */
interface HttpResponseInterface extends ResponseInterface
{
    /**
     * Set response status code
     */
    public function setStatusCode(int $status_code): HttpResponse;
    public function getStatusCode(): int;

    /**
     * Set response content type
     */
    public function setContentType(string $content_type): HttpResponse;
    public function getContentType(): string;

    /**
     * Set response header or add another value to existing header if header allows multiple values
     */
    public function setHeader(string $name, string $value, bool $multiple = false): HttpResponse;
    public function getHeaders(): array;
}
