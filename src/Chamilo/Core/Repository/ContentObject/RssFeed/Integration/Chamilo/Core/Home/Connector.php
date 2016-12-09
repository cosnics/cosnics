<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home;

use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Connector
{

    public function get_rss_feed_objects()
    {
        $options = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session::get_user_id()));
        
        $objects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            RssFeed::class_name(), 
            $condition);
        
        if ($objects->size() == 0)
        {
            $options[0] = Translation::get('CreateRssFeedFirst');
        }
        else
        {
            $options[0] = Translation::get('SelectRssFeed');
            while ($object = $objects->next_result())
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }
        
        return $options;
    }
}
