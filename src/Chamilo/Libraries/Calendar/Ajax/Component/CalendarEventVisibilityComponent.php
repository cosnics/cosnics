<?php
namespace Chamilo\Libraries\Calendar\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Calendar\Ajax\Manager;
use Chamilo\Libraries\Calendar\Event\Visibility;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Calendar\Event\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarEventVisibilityComponent extends Manager
{
    public const PARAM_SOURCE = 'source';

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $source = $this->getPostDataValue(self::PARAM_SOURCE);
        $context = static::CONTEXT . '\Storage\DataClass';
        $visibilityClass = $context . '\Visibility';

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibilityClass, Visibility::PROPERTY_USER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibilityClass, Visibility::PROPERTY_SOURCE),
            new StaticConditionVariable($source)
        );
        $condition = new AndCondition($conditions);

        // Retrieve the visibility object from storage
        $visibility = $this->retrieveVisibility($condition);
        $translator = $this->getTranslator();

        if ($visibility instanceof Visibility)
        {
            if ($visibility->delete())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::error(
                    500, $translator->trans(
                    'ObjectNotDeleted',
                    ['OBJECT' => $translator->trans('Visibility', [], 'Chamilo\Libraries\Calendar')],
                    StringUtilities::LIBRARIES
                )
                );
            }
        }
        else
        {
            $visibility = new $visibilityClass();
            $visibility->setUserId($this->getUser()->getId());
            $visibility->setSource($source);

            if ($visibility->create())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::error(
                    500, $translator->trans(
                    'ObjectNotCreated',
                    ['OBJECT' => $translator->trans('Visibility', [], 'Chamilo\Libraries\Calendar')],
                    StringUtilities::LIBRARIES
                )
                );
            }
        }
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_SOURCE];
    }

    abstract public function retrieveVisibility(Condition $condition): ?Visibility;
}
