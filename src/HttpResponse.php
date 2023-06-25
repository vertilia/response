<?php

declare(strict_types=1);

namespace Vertilia\Response;

/**
 * HTTP response with its status code, headers and content. Allows array access to all validated arguments following
 * predefined filters.
 */
class HttpResponse extends Response implements HttpResponseInterface
{
    protected int $status_code = 200;
    protected ?string $content_type = null;
    protected array $headers = [];

    /**
     * Define response filters and set Content-Type if known
     */
    public function __construct(array $filters, ?string $content_type = null)
    {
        parent::__construct($filters);
        if (isset($content_type)) {
            $this->content_type = $content_type;
        }
    }

    public function setStatusCode(int $status_code): HttpResponse
    {
        $this->status_code = $status_code;
        return $this;
    }

    public function setContentType(string $content_type): HttpResponse
    {
        $this->content_type = $content_type;
        return $this;
    }

    public function setHeader(string $name, string $value, bool $multiple = false): HttpResponse
    {
        $name_lcase = strtolower($name);

        if ($multiple and isset($this->headers[$name_lcase])) {
            if (!is_array($this->headers[$name_lcase])) {
                $this->headers[$name_lcase] = [$this->headers[$name_lcase]];
            }
            $this->headers[$name_lcase][] = $value;
        } else {
            $this->headers[$name_lcase] = $value;
        }

        return $this;
    }

    public function preRender()
    {
        // define response status code
        http_response_code($this->status_code);

        // set Content-Type header if defined
        if (isset($this->content_type)) {
            $this->setHeader('Content-Type', $this->content_type);
        }

        // send all headers
        if ($this->headers) {
            foreach ($this->headers as $h_name => $h_value) {
                if (is_array($h_value)) {
                    foreach ($h_value as $val) {
                        header("$h_name: $val", false);
                    }
                } else {
                    header("$h_name: $h_value");
                }
            }
        }
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function getContentType(): string
    {
        return $this->content_type;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
