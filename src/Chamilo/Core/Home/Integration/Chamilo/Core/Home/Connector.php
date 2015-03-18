<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home;

use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\External;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\StaticContent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 * 
 * @author Hans De Bisschop
 */
class Connector
{

    /**
     * Returns a list of objects for the specified types.
     * 
     * @param array $types
     * @return array
     */
    public static function get_objects($types)
    {
        $result = array();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session :: get_user_id()));
        
        $types_condition = array();
        foreach ($types as $type)
        {
            $types_condition[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE), 
                new StaticConditionVariable($type));
        }
        $conditions[] = new OrCondition($types_condition);
        $condition = new AndCondition($conditions);
        
        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            ContentObject :: class_name(), 
            $condition);
        
        if ($objects->size() == 0)
        {
            $result[0] = Translation :: get('CreateObjectFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $result[$object->get_id()] = $object->get_title();
            }
        }
        
        return $result;
    }

    public function get_external_objects()
    {
        return self :: get_objects(External :: get_supported_types());
    }

    /**
     * Returns a list of objects that can be linked to a static block.
     * 
     * @return array
     */
    public function get_static_objects()
    {
        $result = array();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session :: get_user_id()));
        
        $types = StaticContent :: get_supported_types();
        $types_condition = array();
        foreach ($types as $type)
        {
            $types_condition[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE), 
                new StaticConditionVariable($type));
        }
        $conditions[] = new OrCondition($types_condition);
        $condition = new AndCondition($conditions);
        
        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            ContentObject :: class_name(), 
            $condition);
        
        if ($objects->size() == 0)
        {
            $result[0] = Translation :: get('CreateObjectFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $result[$object->get_id()] = $object->get_title();
            }
        }
        
        return $result;
    }
}
