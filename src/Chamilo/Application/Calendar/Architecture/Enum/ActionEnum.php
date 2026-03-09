<?php
namespace Chamilo\Application\Calendar\Architecture\Enum;

use Chamilo\Application\Calendar\Component\AvailabilityComponent;
use Chamilo\Application\Calendar\Component\BrowseComponent;
use Chamilo\Application\Calendar\Component\ICalComponent;
use Chamilo\Application\Calendar\Component\PrintComponent;
use Chamilo\Application\Calendar\Component\VisibilityComponent;

/**
 * @package Chamilo\Core\Admin\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case AVAILABILITY = 'Availability';
    case BROWSE = 'Browse';
    case ICAL = 'ICal';
    case PRINT = 'Print';
    case VISIBILITY = 'Visibility';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            AvailabilityComponent::class => self::AVAILABILITY,
            BrowseComponent::class => self::BROWSE,
            ICalComponent::class => self::ICAL,
            PrintComponent::class => self::PRINT,
            VisibilityComponent::class => self::VISIBILITY
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
