<?php

namespace Grashoper\GregorianOrdinal\Tests;

use Grashoper\GregorianOrdinal\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{

    public function testFromOrdinal()
    {
         $this->assertEquals([1, 5, 3], Date::fromOrdinal(123));
    }

    public function testToOrdinal()
    {
         $this->assertEquals(123, Date::toOrdinal(1, 5, 3));
    }

}
