<?php

namespace Tests\PhermUI\View\Concerns;

use MilesChou\PhermUI\View\Concerns\Frame;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

class FrameTest extends TestCase
{
    /**
     * @var Frame|\PHPUnit\Framework\MockObject\MockObject
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = $this->getMockForTrait(Frame::class);
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
    public function shouldThrowExceptionWhenCallDefaultWhenIndexOutOfRange(): void
    {
        $this->expectException(OutOfRangeException::class);

        Frame::getBorderDefault(6);
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
    public function shouldChangeOneBorderWhenCallSetBorder(): void
    {
        $this->target->setBorderChar(3, '+');

        $this->assertSame('+', $this->target->getBorderChar(3));
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
