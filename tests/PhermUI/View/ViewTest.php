<?php

namespace Tests\PhermUI\View;

use Illuminate\Container\Container;
use InvalidArgumentException;
use MilesChou\PhermUI\View\View;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class ViewTest extends TestCase
{
    /**
     * @var View
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $container->bind(EventDispatcherInterface::class, $this->getMockClass(EventDispatcherInterface::class));

        $this->target = new View($container);
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectPositionAndSizeWhenConstruct(): void
    {
        $this->target->setPosition(5, 15);
        $this->target->setSize(20, 10);
        $this->target->clear();

        $this->assertSame([5, 15], $this->target->position());
        $this->assertSame([20, 10], $this->target->size());
        $this->assertSame([22, 12], $this->target->frameSize());
    }

    /**
     * @test
     */
    public function shouldUsingFrameSizeWhenInitialBuffer(): void
    {
        $this->target->setPosition(1, 1);
        $this->target->setSize(20, 10);
        $this->target->clear();

        $this->assertCount(12, $this->target->getBuffer());
        $this->assertCount(22, $this->target->getBuffer()[0]);
    }

    /**
     * @test
     */
    public function shouldGetDefaultContentAndTitle(): void
    {
        $this->target->setPosition(1, 1);
        $this->target->setSize(1, 1);
        $this->target->clear();

        $this->assertSame('', $this->target->getContent());
        $this->assertSame('', $this->target->getTitle());
        $this->assertFalse($this->target->hasTitle());
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenSetContent(): void
    {
        $this->target->setPosition(1, 1);
        $this->target->setSize(1, 1);
        $this->target->clear();

        $this->target->setContent('whatever');

        $this->assertSame('whatever', $this->target->getContent());
    }

    /**
     * @test
     */
    public function shouldHasTitleAfterSetTitle(): void
    {
        $this->target->setPosition(1, 1);
        $this->target->setSize(1, 1);
        $this->target->clear();

        // Alias for setTitle() method
        $this->target->title('whatever');

        $this->assertSame('whatever', $this->target->getTitle());
        $this->assertTrue($this->target->hasTitle());
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenWriteBuffer(): void
    {
        $this->target->setPosition(1, 1);
        $this->target->setSize(1, 1);
        $this->target->clear();

        $this->target->write(1, 1, 'x');

        $this->assertSame(['x'], $this->target->getBuffer()[1][1]);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenWriteBufferWithInvalidPosition(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->target->setPosition(1, 1);
        $this->target->setSize(1, 1);
        $this->target->clear();

        $this->target->write(10, 10, 'x');
    }

    /**
     * @test
     */
    public function shouldReturnDefaultBorderSettingAndContent(): void
    {
        $this->assertTrue($this->target->hasBorder());

        $this->assertSame('┐', $this->target->getBorderChar(3));
        $this->assertSame('│', $this->target->getBorderChar(1));
        $this->assertSame('└', $this->target->getBorderChar(4));
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUseDiffBorder(): void
    {
        $this->target->useAsciiBorder();

        $this->assertSame('+', $this->target->getBorderChar(3));
        $this->assertSame('|', $this->target->getBorderChar(1));
        $this->assertSame('+', $this->target->getBorderChar(4));

        $this->target->useDefaultBorder();

        $this->assertSame('┐', $this->target->getBorderChar(3));
        $this->assertSame('│', $this->target->getBorderChar(1));
        $this->assertSame('└', $this->target->getBorderChar(4));
    }

    /**
     * @test
     */
    public function shouldChangeBorderSettingWhenEnableAndDisable(): void
    {
        $this->target->disableBorder();

        $this->assertFalse($this->target->hasBorder());

        $this->target->enableBorder();

        $this->assertTrue($this->target->hasBorder());
    }

    /**
     * @test
     */
    public function shouldReturnCorrectSizeWhenSetTheSize(): void
    {
        $this->target->setSize(20, 10);

        $this->assertSame([20, 10], $this->target->size());
        $this->assertSame([22, 12], $this->target->frameSize());
    }

    /**
     * @test
     */
    public function shouldReturnCorrectPositionWhenSetThePosition(): void
    {
        $this->target->setPosition(5, 15);

        $this->assertSame([5, 15], $this->target->position());
    }
}
