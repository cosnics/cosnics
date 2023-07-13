<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component class responsible for the unsubscribing process of a topic.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class TopicUnsubscribeComponent extends Manager
{

    public function run()
    {
        $success = false;
        $subscribe_id = $this->getRequest()->query->get(self::PARAM_SUBSCRIBE_ID);

        if ($subscribe_id)
        {
            $subscribe = DataManager::retrieve_subscribe($subscribe_id, null);
            $success = $subscribe && $subscribe->delete();
        }

        $message = Translation::get(
            $success ? 'SuccesUnsubscribe' : 'UnSuccesUnSubscribe', null,
            ContentObject::get_content_object_type_namespace('forum_topic')
        );

        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $this->redirectWithMessage($message, !$success, $params);
    }
}
