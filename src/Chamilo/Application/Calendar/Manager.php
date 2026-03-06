<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Component\AvailabilityComponent;
use Chamilo\Application\Calendar\Component\BrowseComponent;
use Chamilo\Application\Calendar\Component\ICalComponent;
use Chamilo\Application\Calendar\Component\PrintComponent;
use Chamilo\Application\Calendar\Component\VisibilityComponent;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_AVAILABILITY = 'Availability';
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_ICAL = 'ICal';
    public const ACTION_PRINT = 'Print';
    public const ACTION_VISIBILITY = 'Visibility';
    public const CONTEXT = __NAMESPACE__;

    public function getApplicationAction(): string
    {
        return match (static::class) {
            AvailabilityComponent::class => self::ACTION_AVAILABILITY,
            BrowseComponent::class => self::ACTION_BROWSE,
            ICalComponent::class => self::ACTION_ICAL,
            PrintComponent::class => self::ACTION_PRINT,
            VisibilityComponent::class => self::ACTION_VISIBILITY
        };
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_BROWSE;
    }

    public function getVisibilityRepository(): VisibilityRepository
    {
        return $this->getService(VisibilityRepository::class);
    }
}
