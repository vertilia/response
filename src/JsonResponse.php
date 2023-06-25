<?php

declare(strict_types=1);

namespace Vertilia\Response;

/**
 * JSON HTTP response of "application/json" content type
 */
class JsonResponse extends HttpResponse
{
    protected ?string $content_type = 'application/json';

    public function __toString(): string
    {
        return json_encode((array)$this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
