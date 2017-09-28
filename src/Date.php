<?php

namespace Grashoper\GregorianOrdinal;

class Date
{
    /**
     * @var int[]
     */
    const DAYS_IN_MONTH = [
        -1,
        31,
        28,
        31,
        30,
        31,
        30,
        31,
        31,
        30,
        31,
        30,
        31,
    ];

    /**
     * @var int[]
     */
    const DAYS_BEFORE_MONTH = [
        -1,
        0,
        31,
        59,
        90,
        120,
        151,
        181,
        212,
        243,
        273,
        304,
        334,
    ];

    public static function fromOrdinal($ordinal)
    {
        return static::ord2ymd($ordinal);
    }

    public static function toOrdinal($year, $month, $day)
    {
        return static::ymd2ord($year, $month, $day);
    }

    private static function ord2ymd($n)
    {
        $n -= 1;
        $_DI400Y = self::daysBeforeYear(401);
        $_DI100Y = self::daysBeforeYear(101);
        $_DI4Y = self::daysBeforeYear(5);
        list($n400, $n) = static::divmod($n, $_DI400Y);
        $year = $n400 * 400 + 1;   # ..., -399, 1, 401, ...

        # Now n is the (non-negative) offset, in days, from January 1 of year, to
        # the desired date.  Now compute how many 100-year cycles precede n.
        # Note that it's possible for n100 to equal 4!  In that case 4 full
        # 100-year cycles precede the desired day, which implies the desired
        # day is December 31 at the end of a 400-year cycle.
        list($n100, $n) = static::divmod($n, $_DI100Y);

        # Now compute how many 4-year cycles precede it.
        list($n4, $n) = static::divmod($n, $_DI4Y);

        # And now how many single years.  Again n1 can be 4, and again meaning
        # that the desired day is December 31 at the end of the 4-year cycle.
        list($n1, $n) = static::divmod($n, 365);

        $year += $n100 * 100 + $n4 * 4 + $n1;
        if ($n1 == 4 or $n100 == 4) {
            assert($n == 0);

            return [$year - 1, 12, 31];
        }

        # Now the year is correct, and n is the offset from January 1.  We find
        # the month via an estimate that's either exact or one too large.
        $leapyear = $n1 == 3 and ($n4 != 24 or $n100 == 3);
        assert($leapyear == date('L', mktime(0, 0, 0, 1, 1, $year)));
        $month = ($n + 50) >> 5;
        $preceding = static::DAYS_BEFORE_MONTH[$month] + ($month > 2 and $leapyear);
        if ($preceding > $n) {  # estimate is too large
            $month -= 1;
            $preceding -= static::DAYS_IN_MONTH[$month] + ($month == 2 and $leapyear);
        }
        $n -= $preceding;
        assert(0 <= $n);
        assert($n < date('t', mktime(0, 0, 0, $month, 1, $year)));

        # Now the year and month are correct, and n is the offset from the
        # start of that month:  we're done!
        return [$year, $month, $n + 1];
    }

    /**
     * Ordinal, considering 01-Jan-0001 as day 1.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    private static function ymd2ord($year, $month, $day)
    {
        assert(1 <= $month && $month <= 12, 'month must be in 1..12');
        $dim = static::daysInMonth($year, $month);
        assert(1 <= $day && $day <= $dim, 'day must be in 1..'.$dim);

        return static::daysBeforeYear($year) +
            static::daysBeforeMonth($year, $month) +
            $day;
    }

    private static function isLeap($year)
    {
        return date('L', mktime(0, 0, 0, 1, 1, $year));
    }

    private static function divmod($a, $b)
    {
        return [intdiv($a, $b), $a % $b];
    }

    /**
     * Number of days before January 1st of year.
     *
     * @param $year
     * @return int
     */
    private static function daysBeforeYear($year)
    {
        $y = $year - 1;

        return $y * 365 + intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400);
    }

    /**
     * Number of days in that month in that year.
     *
     * @param int $year
     * @param int $month
     * @return int
     */
    private static function daysInMonth($year, $month)
    {

        assert(1 <= $month && $month <= 12, $month);
        if ($month == 2 and static::isLeap($year)) {
            return 29;
        }

        return static::DAYS_IN_MONTH[$month];
    }

    /**
     * Number of days in year preceding first day of month.
     *
     * @param int $year
     * @param int $month
     * @return int
     */
    private static function daysBeforeMonth($year, $month)
    {
        assert(1 <= $month && $month <= 12, 'month must be in 1..12');

        return static::DAYS_BEFORE_MONTH[$month] + ($month > 2 && static::isLeap($year));
    }

}
