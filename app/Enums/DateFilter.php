<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DateFilter extends Enum
{
    const CurrentWeek  = 1;
    const CurrentMonth = 2;
    const LastThreeMonth   = 3;
    const LastSixMonth   = 4;

}
