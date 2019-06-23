<?php

namespace Tests;

use MilesChou\Phcui\Builder;
use MilesChou\Phcui\Cui;
use PHPUnit\Framework\TestCase;

class CuiTest extends TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf(Cui::class, (new Builder())->build());
    }
}
