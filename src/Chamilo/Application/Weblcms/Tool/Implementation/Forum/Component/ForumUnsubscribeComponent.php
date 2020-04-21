<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager as ForumDataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * This represents the component for unsubscribing a forum at forum application level.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent ForumManagerForumUnsubscribeComponent
 */
class ForumUnsubscribeComponent extends Manager
{

    public function run()
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $this->publication_id);
        
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->publication_id);
        
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }
        
        $success = false;
        $subscribe_id = Request::get(self::PARAM_SUBSCRIBE_ID);
        
        if ($subscribe_id)
        {
            $subscribe = ForumDataManager::retrieve_subscribe($subscribe_id, $this->get_user_id());
            $success = $subscribe && $subscribe->delete();
        }
        
        $message = Translation::get(
            $success ? "SuccesUnSubscribe" : "UnSuccesUnSubscribe", 
            null, 
            ContentObject::get_content_object_type_namespace('Forum'));
        
        $this->redirect(
            $message, 
            ! $success, 
            array(self::PARAM_ACTION => self::ACTION_BROWSE), 
            array(self::PARAM_PUBLICATION_ID));
    }
}
