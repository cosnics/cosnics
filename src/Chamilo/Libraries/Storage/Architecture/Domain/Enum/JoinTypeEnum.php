<?php

namespace Chamilo\Libraries\Storage\Architecture\Domain\Enum;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum JoinTypeEnum: int
{
    case LEFT = 2;
    case NORMAL = 1;
    case RIGHT = 3;
}
