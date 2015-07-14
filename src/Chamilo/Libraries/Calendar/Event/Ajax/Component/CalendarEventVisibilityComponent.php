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
        $visibility_class = $context . '\Visibility';

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibility_class, Visibility :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($visibility_class, Visibility :: PROPERTY_SOURCE),
            new StaticConditionVariable($source));
        $condition = new AndCondition($conditions);

        // Retrieve the visibility object from storage
        $visibility = $this->retrieve_visibility($condition);

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

            $visibility = new $visibility_class();
            $visibility->set_user_id($this->get_user_id());
            $visibility->set_source($source);
            $this->set_visibility($visibility, $data);

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
     * @param Condition $condition
     * @return \libraries\calendar\event\Visibility
     */
    abstract function retrieve_visibility(Condition $condition);

    /**
     *
     * @param \libraries\calendar\event\Visibility $visibility
     * @param string[] $data
     * @return \libraries\calendar\event\Visibility
     */
    abstract function set_visibility($visibility, $data = array());
}
