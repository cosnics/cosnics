<?php

namespace Chamilo\Core\User\Architecture\Enum;

/**
 * @package Chamilo\Core\User\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum UserActivityTypeEnum: int
{
    case CREATED = 1;
    case DELETED = 2;
    case PASSWORD_RESET = 5;
    case REGISTERED = 7;
    case UPDATED = 8;
}
