<?php

namespace MilesChou\PhermUI\View\Concerns;

trait Configuration
{
    /**
     * @var bool
     */
    private $instantRender = false;

    /**
     * @return static
     */
    public function disableInstantRender()
    {
        $this->instantRender = true;
        return $this;
    }

    /**
     * @return static
     */
    public function enableInstantRender()
    {
        $this->instantRender = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInstantRender(): bool
    {
        return $this->instantRender;
    }
}
