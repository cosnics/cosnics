<?php

namespace Chamilo\Libraries\Storage\Architecture\Domain\Enum;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum FunctionTypeEnum: int
{
    case AVERAGE = 6;
    case COUNT = 2;
    case DISTINCT = 5;
    case MAX = 4;
    case MIN = 3;
    case SUM = 1;
    case IDENTITY = 7;
}
