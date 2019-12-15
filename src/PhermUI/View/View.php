<?php

namespace MilesChou\PhermUI\View;

use InvalidArgumentException;
use MilesChou\Pherm\Concerns\BufferTrait;
use MilesChou\Pherm\Concerns\SizeAwareTrait;
use MilesChou\PhermUI\Events\ViewChange;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class View implements ViewInterface
{
    use BufferTrait;
    use SizeAwareTrait {
        setSize as private setSizeRaw;
    }

    /**
     * @var bool
     */
    private $border = true;

    /**
     * @var array [horizontal, vertical, top-left, top-right, bottom-left, bottom-right]
     */
    private $borderChars = ['─', '│', '┌', '┐', '└', '┘'];

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int
     */
    private $positionX;

    /**
     * @var int
     */
    private $positionY;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function clear(): ViewInterface
    {
        $this->resetBuffer();

        return $this;
    }

    /**
     * @return View
     */
    public function disableBorder(): View
    {
        $this->border = false;

        $this->eventDispatcher->dispatch(new ViewChange($this));

        return $this;
    }

    /**
     * @return View
     */
    public function enableBorder(): View
    {
        $this->border = true;

        $this->eventDispatcher->dispatch(new ViewChange($this));

        return $this;
    }

    /**
     * @return array
     */
    public function frameSize(): array
    {
        return [$this->frameSizeX(), $this->frameSizeY()];
    }

    /**
     * @return int
     */
    public function frameSizeX(): int
    {
        return $this->width + 2;
    }

    /**
     * @return int
     */
    public function frameSizeY(): int
    {
        return $this->height + 2;
    }

    /**
     * @param int $key
     * @return string
     */
    public function getBorderChar(int $key): string
    {
        return $this->borderChars[$key];
    }

    /**
     * @inheritDoc
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function hasBorder(): bool
    {
        return $this->border;
    }

    /**
     * @return bool
     */
    public function hasTitle(): bool
    {
        return $this->title !== '';
    }

    /**
     * @param string $content
     * @return View
     */
    public function setContent(string $content): View
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $title
     * @return static
     */
    public function setTitle(string $title): ViewInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Alias for setTitle()
     *
     * @param string $title
     * @return View
     */
    public function title(string $title): View
    {
        return $this->setTitle($title);
    }

    /**
     * @param int $y
     * @param int $x
     * @param string|null $char
     */
    public function write(int $x, int $y, $char): void
    {
        if (!isset($this->buffer[$y][$x])) {
            throw new InvalidArgumentException("Invalid x '$x' or y '$y'");
        }

        $this->buffer[$y][$x] = [$char];
    }

    /**
     * @return array
     */
    public function position(): array
    {
        return [$this->positionX, $this->positionY];
    }

    /**
     * @param int|array $key
     * @param string $char
     * @return View
     */
    public function setBorderChar($key, $char = null): View
    {
        if (is_array($key)) {
            $this->borderChars = $key;
        } else {
            $this->borderChars[$key] = $char;
        }

        $this->eventDispatcher->dispatch(new ViewChange($this));

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     */
    public function setPosition(int $x, int $y): void
    {
        $this->positionX = $x;
        $this->positionY = $y;

        $this->eventDispatcher->dispatch(new ViewChange($this));
    }

    /**
     * @param int $width
     * @param int $height
     * @return View
     */
    public function setSize(int $width, int $height): View
    {
        $this->setSizeRaw($width, $height);

        $this->eventDispatcher->dispatch(new ViewChange($this));

        return $this;
    }

    /**
     * @return View
     */
    public function useAsciiBorder(): View
    {
        return $this->setBorderChar(['-', '|', '+', '+', '+', '+']);
    }

    /**
     * @return View
     */
    public function useDefaultBorder(): View
    {
        return $this->setBorderChar(['─', '│', '┌', '┐', '└', '┘']);
    }

    private function resetBuffer(): void
    {
        [$frameSizeX, $frameSizeY] = $this->frameSize();

        // $buffer[$y] = []
        $this->buffer = array_fill(0, $frameSizeY, null);

        // $buffer[$y][$x] = []
        $this->buffer = array_map(function () use ($frameSizeX) {
            return array_fill(0, $frameSizeX, [' ']);
        }, $this->buffer);
    }
}
