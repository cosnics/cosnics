<?php

namespace Chamilo\Libraries\Storage\Architecture\Domain\Enum;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ComparisonTypeEnum: int
{
    case EQUAL = 5;
    case GREATER_THAN = 3;
    case GREATER_THAN_OR_EQUAL = 4;
    case LESS_THAN = 1;
    case LESS_THAN_OR_EQUAL = 2;
}
