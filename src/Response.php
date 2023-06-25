<?php

declare(strict_types=1);

namespace Vertilia\Response;

use Vertilia\ValidArray\ValidArray;

class Response extends ValidArray implements ResponseInterface
{
    /**
     * Allow children to do preliminary validations before rendering response data
     */
    public function preRender()
    {
    }

    public function __toString(): string
    {
        return serialize((array)$this);
    }

    public function render()
    {
        $this->preRender();
        echo $this;
    }
}
