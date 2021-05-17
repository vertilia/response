<?php
declare(strict_types=1);

namespace Vertilia\Response;

/**
 * Represents JSON HTTP response of "application/json" content type
 */
class JsonResponse extends HttpResponse
{
    /** @var string */
    protected string $content_type = 'application/json';

    public function render()
    {
        $this->preRender();
        echo json_encode((array)$this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
