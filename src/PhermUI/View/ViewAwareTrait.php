<?php

namespace MilesChou\PhermUI\View;

/**
 * Implement ViewInterface and proxy to baseView
 *
 * @see ViewInterface
 */
trait ViewAwareTrait
{
    /**
     * @var ViewInterface
     */
    private $baseView;

    public function frameSize(): array
    {
        return $this->baseView->frameSize();
    }

    public function getBorderChar(int $key): string
    {
        return $this->baseView->getBorderChar($key);
    }

    public function getBuffer(): array
    {
        return $this->baseView->getBuffer();
    }

    public function getContent(): string
    {
        return $this->baseView->getContent();
    }

    public function getTitle(): string
    {
        return $this->baseView->getTitle();
    }

    public function hasBorder(): bool
    {
        return $this->baseView->hasBorder();
    }

    public function hasTitle(): bool
    {
        return $this->baseView->hasTitle();
    }

    public function position(): array
    {
        return $this->baseView->position();
    }

    /**
     * @param ViewInterface $baseView
     * @return static
     */
    public function setBaseView(ViewInterface $baseView)
    {
        $this->baseView = $baseView;

        return $this;
    }

    public function setTitle(string $title)
    {
        $this->baseView->setTitle($title);

        return $this;
    }

    public function size(): array
    {
        return $this->baseView->size();
    }

    public function writeBuffer(int $x, int $y, $char): void
    {
        $this->baseView->writeBuffer($x, $y, $char);
    }
}
