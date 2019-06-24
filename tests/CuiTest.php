<?php

namespace Tests;

use MilesChou\PhermUI\Builder;
use MilesChou\PhermUI\Cui;
use PHPUnit\Framework\TestCase;

class CuiTest extends TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf(Cui::class, (new Builder())->build());
    }
}
