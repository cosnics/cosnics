<?php
namespace Chamilo\Application\Calendar\Ajax\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\StorageParameters;
use Exception;

class CalendarEventVisibilityComponent
    extends \Chamilo\Libraries\Calendar\Ajax\Component\CalendarEventVisibilityComponent
{
    public const CONTEXT = Manager::CONTEXT;

    public function retrieveVisibility(Condition $condition): ?\Chamilo\Libraries\Calendar\Event\Visibility
    {
        try
        {
            return $this->getDataClassRepository()->retrieve(
                Visibility::class, new StorageParameters(condition: $condition)
            );
        }
        catch (Exception)
        {
            return null;
        }
    }
}
