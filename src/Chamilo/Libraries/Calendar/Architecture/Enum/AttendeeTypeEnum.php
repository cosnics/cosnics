<?php

namespace Chamilo\Libraries\Calendar\Architecture\Enum;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum AttendeeTypeEnum: int
{
    case OPTIONAL = 2;
    case ORGANIZER = 4;
    case REQUIRED = 1;
    case RESOURCE = 3;

    public function getICal(): ?string
    {
        return match ($this) {
            AttendeeTypeEnum::OPTIONAL => 'OPT-PARTICIPANT',
            AttendeeTypeEnum::ORGANIZER => 'CHAIR',
            AttendeeTypeEnum::REQUIRED => 'REQ-PARTICIPANT',
            default => null
        };
    }
}
