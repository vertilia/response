<?php
declare(strict_types=1);

namespace Vertilia\Response;

/**
 * Represents HTML HTTP response of "plain/html" content type. Exposes $content
 * public property which may be used as a string buffer to collect output or
 * as a file handler to receive output via fputs() and alike. If opened as
 * 'php://temp' then stores up to 2M in memory, then automatically switches to
 * disk file.
 */
class HtmlResponse extends HttpResponse
{
    /** @var string */
    protected string $content_type = 'text/html';
    /** @var Closure|resource|string a Closure, a file handler opened with fopen() or a string */
    public $content;

    public function render()
    {
        $this->preRender();

        if (is_scalar($this->content)) {
            echo $this->content;
        } elseif (is_resource($this->content)) {
            rewind($this->content);
            fpassthru($this->content);
            fclose($this->content);
        } elseif ($this->content instanceof \Closure) {
            ($this->content)();
        }
    }
}
