<?php

namespace Chamilo\Libraries\Calendar\Architecture\Enum;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ResponseStatusEnum: int
{
    case ACCEPTED = 1;
    case DECLINED = 2;
    case NONE = 5;
    case ORGANIZER = 4;
    case TENTATIVE = 3;

    public function getICal(): ?string
    {
        return match ($this) {
            ResponseStatusEnum::ACCEPTED => 'ACCEPTED',
            ResponseStatusEnum::DECLINED => 'DECLINED',
            ResponseStatusEnum::TENTATIVE => 'TENTATIVE',
            default => null
        };
    }
}
