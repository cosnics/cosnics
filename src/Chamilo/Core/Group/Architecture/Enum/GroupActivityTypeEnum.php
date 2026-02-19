<?php

namespace Chamilo\Core\Group\Architecture\Enum;

/**
 * @package Chamilo\Core\Group\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum GroupActivityTypeEnum: int
{
    case CREATED = 1;
    case DELETED = 2;
    case MOVED = 4;
    case SUBSCRIBED = 5;
    case TRUNCATED = 3;
    case UNSUBSCRIBED = 6;
    case UPDATED = 7;
}
