<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_AVAILABILITY = 'Availability';
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_ICAL = 'ICal';
    public const ACTION_PRINT = 'Printer';
    public const ACTION_VISIBILITY = 'Visibility';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultAction(): string
    {
        return self::DEFAULT_ACTION;
    }

    public function getVisibilityRepository(): VisibilityRepository
    {
        return $this->getService(VisibilityRepository::class);
    }
}
