<?php
namespace App\Enums;

enum BookingDurationLimits : int
{
    case MIN = 30;
    case MAX = 1440; // 60*24
}
