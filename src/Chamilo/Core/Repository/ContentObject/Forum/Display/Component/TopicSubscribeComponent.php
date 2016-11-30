<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component class responsible for the subscribing process of a topic.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class TopicSubscribeComponent extends Manager
{

    public function run()
    {
        $topic = $this->get_selected_complex_content_object_item();
        
        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $subscribe_exists = DataManager::retrieve_subscribe($this->get_user_id(), $topic->get_ref());
        if (! $subscribe_exists)
        {
            
            $succes = DataManager::create_subscribe($this->get_user_id(), $topic->get_ref());
            if ($succes)
            {
                
                $message = Translation::get(
                    "SuccesSubscribe", 
                    null, 
                    ContentObject::get_content_object_type_namespace('forum_topic'));
                
                $this->redirect($message, false, $params);
            }
            else
            {
                $message = Translation::get(
                    'UnSuccesSubscribe', 
                    null, 
                    ContentObject::get_content_object_type_namespace('forum_topic'));
                $this->redirect($message, true, $params);
            }
        }
        else
        {
            $message = Translation::get(
                'UnSuccesSubscribe', 
                null, 
                ContentObject::get_content_object_type_namespace('forum_topic'));
            $this->redirect($message, true, $params);
        }
    }
}
