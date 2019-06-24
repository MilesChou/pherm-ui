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
    public function shouldReturnDefaultBorderContent()
    {
        $this->assertSame('┐', $this->target->getBorder(3));
        $this->assertSame('│', $this->target->getBorder(1));
        $this->assertSame('└', $this->target->getBorder(4));

        $this->assertSame('┐', Border::getBorderDefault(3));
        $this->assertSame('│', Border::getBorderDefault(1));
        $this->assertSame('└', Border::getBorderDefault(4));
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

        $this->assertSame('+', $this->target->getBorder(3));
        $this->assertSame('|', $this->target->getBorder(1));
        $this->assertSame('+', $this->target->getBorder(4));

        $this->target->useDefaultBorder();

        $this->assertSame('┐', $this->target->getBorder(3));
        $this->assertSame('│', $this->target->getBorder(1));
        $this->assertSame('└', $this->target->getBorder(4));
    }

    /**
     * @test
     */
    public function shouldChangeOneBorderWhenCallSetBorder()
    {
        $this->target->setBorder(3, '+');

        $this->assertSame('+', $this->target->getBorder(3));
        $this->assertSame('│', $this->target->getBorder(1));
        $this->assertSame('└', $this->target->getBorder(4));
    }
}
