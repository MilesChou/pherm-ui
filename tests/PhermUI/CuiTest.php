<?php

namespace Tests\PhermUI;

use MilesChou\PhermUI\Builder;
use MilesChou\PhermUI\PhermUI;
use PHPUnit\Framework\TestCase;

class CuiTest extends TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf(PhermUI::class, (new Builder())->build());
    }
}
