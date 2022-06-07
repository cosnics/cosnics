<?php
namespace Chamilo\Libraries\Calendar\Event\Ajax\Component;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Calendar\Event\Ajax\Manager;
use Chamilo\Libraries\Calendar\Event\Visibility;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarEventVisibilityComponent extends Manager
{
    const PARAM_SOURCE = 'source';

    /**
     * @throws \ReflectionException
     */
    public function run()
    {
        $source = $this->getPostDataValue(self::PARAM_SOURCE);
        $context = ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 2) . '\Storage\DataClass';
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

        $result = new JsonAjaxResult();

        if ($visibility instanceof Visibility)
        {
            if ($visibility->delete())
            {
                $result->success();
            }
            else
            {
                $result->error(
                    500, $translator->trans(
                    'ObjectNotDeleted',
                    array('OBJECT' => $translator->trans('Visibility', [], 'Chamilo\Libraries\Calendar')),
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
                $result->success();
            }
            else
            {
                $result->error(
                    500, $translator->trans(
                    'ObjectNotCreated',
                    array('OBJECT' => $translator->trans('Visibility', [], 'Chamilo\Libraries\Calendar')),
                    StringUtilities::LIBRARIES
                )
                );
            }
        }

        $result->display();
    }

    public function getRequiredPostParameters()
    {
        return array(self::PARAM_SOURCE);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Calendar\Event\Visibility
     */
    abstract function retrieveVisibility(Condition $condition);
}
