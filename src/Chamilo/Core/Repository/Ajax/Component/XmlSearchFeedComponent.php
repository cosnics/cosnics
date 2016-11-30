<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class XmlSearchFeedComponent extends \Chamilo\Core\Repository\Ajax\Manager
{

    function run()
    {
        $conditions = array();
        
        $query_condition = Utilities::query_to_condition(
            Request::post('queryString'), 
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        
        if (isset($query_condition))
        {
            $conditions[] = $query_condition;
        }
        
        $owner_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session::get_user_id()));
        $conditions[] = $owner_condition;
        
        $category_type_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE), 
            new StaticConditionVariable('category'));
        $conditions[] = new NotCondition($category_type_condition);
        
        $condition = new AndCondition($conditions);
        
        $objects = DataManager::retrieve_active_content_objects(
            ContentObject::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($lo = $objects->next_result())
        {
            echo '<li onclick="fill(\'' . $lo->get_title() . '\');">';
            echo $lo->get_title();
            echo '</li>';
        }
    }
}
