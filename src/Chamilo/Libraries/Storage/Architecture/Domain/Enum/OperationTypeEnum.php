<?php

namespace Chamilo\Libraries\Storage\Architecture\Domain\Enum;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum OperationTypeEnum: int
{
    case ADDITION = 1;
    case BITWISE_AND = 5;
    case BITWISE_OR = 6;
    case DIVISION = 4;
    case MINUS = 2;
    case MULTIPLICATION = 3;
}
