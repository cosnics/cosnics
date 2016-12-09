<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SearchCompleteComponent extends \Chamilo\Core\Repository\Ajax\Manager
{

    public function run()
    {
        $response = array();
        
        $query = Request::get('term');
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session::get_user_id()));
        $or_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE), 
            '*' . $query . '*');
        $or_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION), 
            '*' . $query . '*');
        $conditions[] = new OrCondition($or_conditions);
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            null, 
            null, 
            array(
                new OrderBy(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE))));
        $objects = DataManager::retrieve_active_content_objects(ContentObject::class_name(), $parameters);
        
        while ($object = $objects->next_result())
        {
            $response[] = array(
                'id' => $object->get_id(), 
                'label' => StringUtilities::getInstance()->truncate($object->get_title(), 23), 
                'value' => $object->get_title());
        }
        
        echo json_encode($response);
    }
}