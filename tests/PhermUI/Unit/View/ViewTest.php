<?php

namespace Tests\PhermUI\View;

use MilesChou\PhermUI\View\Concerns\Frame;
use MilesChou\PhermUI\View\View;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnCorrectPositionAndSizeWhenConstruct(): void
    {
        $target = new View(5, 15, 20, 10);

        $this->assertSame([5, 15], $target->position());
        $this->assertSame([20, 10], $target->size());
        $this->assertSame([22, 12], $target->frameSize());
    }

    /**
     * @test
     */
    public function shouldUsingFrameSizeWhenInitialBuffer(): void
    {
        $target = new View(1, 1, 20, 10);

        $this->assertCount(12, $target->getBuffer());
        $this->assertCount(22, $target->getBuffer()[0]);
    }

    /**
     * @test
     */
    public function shouldGetDefaultContentAndTitle(): void
    {
        $target = new View(1, 1, 1, 1);

        $this->assertSame('', $target->getContent());
        $this->assertSame('', $target->getTitle());
        $this->assertFalse($target->hasTitle());
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenSetContent(): void
    {
        $target = new View(1, 1, 1, 1);

        $target->setContent('whatever');

        $this->assertSame('whatever', $target->getContent());
    }

    /**
     * @test
     */
    public function shouldHasTitleAfterSetTitle(): void
    {
        $target = new View(1, 1, 1, 1);

        // Alias for setTitle() method
        $target->title('whatever');

        $this->assertSame('whatever', $target->getTitle());
        $this->assertTrue($target->hasTitle());
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenWriteBuffer(): void
    {
        $target = new View(1, 1, 1, 1);

        $target->writeBuffer(1, 1, 'x');

        $this->assertSame(['x'], $target->getBuffer()[1][1]);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenWriteBufferWithInvalidPosition(): void
    {
        $target = new View(1, 1, 1, 1);

        $target->writeBuffer(10, 10, 'x');
    }
}
