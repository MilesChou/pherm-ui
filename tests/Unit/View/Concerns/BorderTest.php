<?php

namespace Tests\Unit\View\Concerns;

use MilesChou\PhermUI\View\Concerns\Border;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

class BorderTest extends TestCase
{
    /**
     * @var Border|\PHPUnit\Framework\MockObject\MockObject
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = $this->getMockForTrait(Border::class);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultBorderSettingAndContent()
    {
        $this->assertTrue($this->target->hasBorder());

        $this->assertSame('┐', $this->target->getBorderChar(3));
        $this->assertSame('│', $this->target->getBorderChar(1));
        $this->assertSame('└', $this->target->getBorderChar(4));
    }

    /**
     * @test
     * @expectedException OutOfRangeException
     */
    public function shouldThrowExceptionWhenCallDefaultWhenIndexOutOfRange()
    {
        Border::getBorderDefault(6);
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUseDiffBorder()
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
    public function shouldChangeOneBorderWhenCallSetBorder()
    {
        $this->target->setBorderChar(3, '+');

        $this->assertSame('+', $this->target->getBorderChar(3));
        $this->assertSame('│', $this->target->getBorderChar(1));
        $this->assertSame('└', $this->target->getBorderChar(4));
    }

    /**
     * @test
     */
    public function shouldChangeBorderSettingWhenEnableAndDisable()
    {
        $this->target->disableBorder();

        $this->assertFalse($this->target->hasBorder());

        $this->target->enableBorder();

        $this->assertTrue($this->target->hasBorder());
    }
}
