<?php
declare(strict_types=1);

namespace Vertilia\Response;

use Vertilia\ValidArray\ValidArray;

/**
 * Represents HTTP response with its status code, headers and content. Allows
 * array access to all validated arguments following predefined filters.
 */
class HttpResponse extends ValidArray implements HttpResponseInterface
{
    /** @var int */
    protected $status_code = 200;
    /** @var string */
    protected $content_type;
    /** @var array */
    protected $headers = [];

    /**
     * Defines response filters and sets Content-Type if defined
     *
     * @param array $filters
     * @param string $content_type
     */
    public function __construct(array $filters, string $content_type = null)
    {
        parent::__construct($filters);
        if (isset($content_type)) {
            $this->content_type = $content_type;
        }
    }

    /**
     * Sets response status code
     *
     * @param int $status_code
     * @return \Vertilia\Response\HttpResponse
     */
    public function setStatusCode(int $status_code): HttpResponse
    {
        $this->status_code = $status_code;
        return $this;
    }

    /**
     * Sets response content type
     *
     * @param string $content_type
     * @return \Vertilia\Response\HttpResponse
     */
    public function setContentType(string $content_type): HttpResponse
    {
        $this->content_type = $content_type;
        return $this;
    }

    /**
     * Sets response header or adds another value to existing header if header
     * allows multiple values
     *
     * @param string $name
     * @param string $value
     * @param bool $multiple
     * @return \Vertilia\Response\HttpResponse
     */
    public function setHeader(string $name, string $value, bool $multiple = false): HttpResponse
    {
        $name_lcase = \strtolower($name);

        if ($multiple and isset($this->headers[$name_lcase])) {
            if (!\is_array($this->headers[$name_lcase])) {
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
        \http_response_code($this->status_code);

        // set Content-Type header if defined
        if (isset($this->content_type)) {
            $this->setHeader('Content-Type', $this->content_type);
        }

        // send all headers
        if ($this->headers) {
            foreach ($this->headers as $h_name => $h_value) {
                if (\is_array($h_value)) {
                    foreach ($h_value as $val) {
                        \header("$h_name: $val", false);
                    }
                } else {
                    \header("$h_name: $h_value");
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

    public function render()
    {
        $this->preRender();
    }
}
