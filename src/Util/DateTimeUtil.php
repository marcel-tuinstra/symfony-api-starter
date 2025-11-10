<?php

namespace App\Util;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class DateTimeUtil
{
    public static function nowUtc(): DateTime
    {
        return new DateTime('now', new DateTimeZone('UTC'));
    }

    public static function nowUtcAsImmutable(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
