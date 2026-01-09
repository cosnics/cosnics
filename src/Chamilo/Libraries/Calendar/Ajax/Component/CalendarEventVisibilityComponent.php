<?php
namespace Chamilo\Libraries\Calendar\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Calendar\Ajax\Manager;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilityServiceInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Calendar\Event\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarEventVisibilityComponent extends Manager
{
    public const PARAM_SOURCE = 'source';

    public function run()
    {
        $source = $this->getRequest()->getFromQueryOrRequest(self::PARAM_SOURCE);

        if ($this->getVisibilityService()->changeVisibility($this->getUser()->getId(), $source))
        {
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::error(
                500, $this->getTranslator()->trans(
                'VisibilityNotChanged', [], StringUtilities::LIBRARIES
            )
            );
        }
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_SOURCE];
    }

    abstract public function getVisibilityService(): VisibilityServiceInterface;
}
