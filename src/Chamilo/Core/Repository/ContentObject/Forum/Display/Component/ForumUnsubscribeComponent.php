<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ForumSubscribe;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component class responsible for the unsubscrbing of a forum.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumUnsubscribeComponent extends Manager
{

    /**
     * this function checks if their is a subscribe and if their is one deletes it
     */
    public function run()
    {
        $succes = false;
        $subscribe_id = Request::get(self::PARAM_SUBSCRIBE_ID);
        
        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        if ($subscribe_id)
        {
            $subscribe = DataManager::retrieve_by_id(ForumSubscribe::class_name(), $subscribe_id);
            $succes = $subscribe->delete();
            if ($succes)
            {
                $message = Translation::get("SuccesUnSubscribe");
            }
            else
            {
                $message = Translation::get("UnSuccesUnSubscribe");
            }
        }
        else
        {
            $message = Translation::get("UnSuccesUnSubscribe");
        }
        
        $this->redirect($message, ($succes ? false : true), $params);
    }
}
