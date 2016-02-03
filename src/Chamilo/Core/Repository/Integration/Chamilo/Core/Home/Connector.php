<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home;

use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type\Streaming;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: repository_connector.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.block.connectors
 */

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
            \Chamilo\Core\Repository\Storage\DataClass\ContentObject :: class_name(), 
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

    public function get_rss_feed_objects()
    {
        $options = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session :: get_user_id()));
        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            RssFeed :: class_name(), 
            $condition);
        
        if ($objects->size() == 0)
        {
            $options[0] = Translation :: get('CreateRssFeedFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }
        
        return $options;
    }

    public function get_link_objects()
    {
        $options = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session :: get_user_id()));
        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            Link :: class_name(), 
            $condition);
        
        if ($objects->size() == 0)
        {
            $options[0] = Translation :: get('CreateLinkFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }
        
        return $options;
    }

    /**
     * Returns a list of objects that can be linked to a streaming block.
     * 
     * @return array
     */
    public function get_streaming_objects()
    {
        return self :: get_objects(Streaming :: get_supported_types());
    }
}
