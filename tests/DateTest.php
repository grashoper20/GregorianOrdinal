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

    public function testTimeFromOrdinal()
    {
        $time = Date::timeFromOrdinal(729832);
        $this->assertEquals(921801600, $time);
    }

    public function testDateFromOrdinal()
    {
        $date_time = Date::dateFromOrdinal(729832);
        $this->assertEquals(921801600, $date_time->getTimestamp());
    }

    public function testOrdinalFromTime()
    {
        $this->assertEquals(729832, Date::ordinalFromTime(921801600));
    }

    public function testOrdinalFromDate()
    {
        $date_time = new \DateTime();
        $date_time->setTimestamp(921801600);
        $this->assertEquals(729832, Date::ordinalFromDate($date_time));
    }

}
