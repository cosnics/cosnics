<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.complex_display.forum.component When a topic is deleted (outside the repository this is the
 *          component
 * @author  Mattias De Pauw
 */
class ForumTopicDeleterComponent extends Manager
{

    public function run()
    {
        $topic = $this->get_selected_complex_content_object_item();

        if ($this->get_user()->get_id() == $topic->get_user_id() || $this->get_user()->isPlatformAdmin() ||
            $this->is_forum_manager($this->get_user()))
        {
            $topic = $this->get_selected_complex_content_object_item();

            $params = [];

            // dump($topic->get_parent_object());dump($this->get_root_content_object());exit;
            if ($topic->get_parent_object()->get_id() == $this->get_root_content_object()->get_id())
            {
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
                $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
                $params[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            }
            else
            {
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
                $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->getRequest()->query->get('parent_cloi');
                $params[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            }

            $success = $topic->delete();

            $message = htmlentities(
                Translation::get(
                    ($success ? 'ObjectDeleted' : 'ObjectNotDeleted'), ['OBJECT' => Translation::get('ForumTopic')],
                    StringUtilities::LIBRARIES
                )
            );

            $this->redirectWithMessage($message, !$success, $params);
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
