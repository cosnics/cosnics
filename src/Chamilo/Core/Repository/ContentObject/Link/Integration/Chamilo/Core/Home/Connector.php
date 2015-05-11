<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.content_object.link.block
 * @author Hans De Bisschop
 */
class Connector
{

    public function get_link_objects()
    {
        $options = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Repository\Storage\DataClass\ContentObject :: class_name(), 
                \Chamilo\Core\Repository\Storage\DataClass\ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable(Session :: get_user_id()));
        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            \Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link :: class_name(), 
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
}
