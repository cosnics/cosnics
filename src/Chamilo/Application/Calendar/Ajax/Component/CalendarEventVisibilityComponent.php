<?php
namespace Chamilo\Application\Calendar\Ajax\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\VisibilityService;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilityServiceInterface;

class CalendarEventVisibilityComponent
    extends \Chamilo\Libraries\Calendar\Ajax\Component\CalendarEventVisibilityComponent
{
    public const CONTEXT = Manager::CONTEXT;

    public function getVisibilityService(): VisibilityServiceInterface
    {
        return $this->getService(VisibilityService::class);
    }
}
