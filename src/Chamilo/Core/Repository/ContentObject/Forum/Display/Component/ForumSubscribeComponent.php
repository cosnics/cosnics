<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component class responsible for the subscribing process of a forum.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent ForumSubscribeComponent
 */
class ForumSubscribeComponent extends Manager
{

    public function run()
    {
        $succes = false;
        
        $forum = $this->get_selected_complex_content_object_item();
        
        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $succes = DataManager::create_subscribe(
            $this->get_user_id(), 
            $forum->get_ref());
        
        if ($succes)
        {
            $message = Translation::get("SuccesSubscribe");
            $this->redirect($message, false, $params);
        }
        else
        {
            $message = Translation::get("UnSuccesSubscribe");
            $this->redirect($message, true, $params);
        }
    }
}
