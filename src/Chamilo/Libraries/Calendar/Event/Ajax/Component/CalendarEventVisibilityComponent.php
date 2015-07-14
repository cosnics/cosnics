<?php
namespace Chamilo\Libraries\Calendar\Event\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Calendar\Event\Visibility;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class CalendarEventVisibilityComponent extends \Chamilo\Libraries\Calendar\Event\Ajax\Manager
{
    const PARAM_SOURCE = 'source';
    const PARAM_DATA = 'data';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_SOURCE);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $source = $this->getPostDataValue(self :: PARAM_SOURCE);
        $context = ClassnameUtilities :: getNamespaceParent(static :: context(), 2) . '\Storage\DataClass';
        $visibilityClass = $context . '\Visibility';

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibilityClass, Visibility :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibilityClass, Visibility :: PROPERTY_SOURCE),
            new StaticConditionVariable($source));
        $condition = new AndCondition($conditions);

        // Retrieve the visibility object from storage
        $visibility = $this->retrieveVisibility($condition);

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
                    500,
                    Translation :: get(
                        'ObjectNotDeleted',
                        array('OBJECT' => Translation :: get('Visibility')),
                        Utilities :: COMMON_LIBRARIES));
            }
        }
        else
        {
            $data = $this->getPostDataValue(self :: PARAM_DATA);

            $visibility = new $visibilityClass();
            $visibility->setUserId($this->get_user_id());
            $visibility->setSource($source);
            $this->setVisibility($visibility, $data);

            if ($visibility->create())
            {
                $result->success();
            }
            else
            {
                $result->error(
                    500,
                    Translation :: get(
                        'ObjectNotCreated',
                        array('OBJECT' => Translation :: get('Visibility')),
                        Utilities :: COMMON_LIBRARIES));
            }
        }

        $result->display();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return \Chamilo\Libraries\Calendar\Event\Visibility
     */
    abstract function retrieveVisibility(Condition $condition);

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Visibility $visibility
     * @param string[] $data
     */
    abstract function setVisibility(Visibility $visibility = null, $data = array());
}
