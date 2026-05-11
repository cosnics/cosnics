<?php

namespace Chamilo\Core\User\Architecture\Enum;

/**
 * @package Chamilo\Core\User\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum UserAuthenticationActivityTypeEnum: int
{
    case LOGIN = 1;
    case LOGOUT = 2;
}
