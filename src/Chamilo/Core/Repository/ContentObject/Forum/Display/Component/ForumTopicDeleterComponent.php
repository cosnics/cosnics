<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_display.forum.component When a topic is deleted (outside the repository this is the
 *          component
 * @author Mattias De Pauw
 */
class ForumTopicDeleterComponent extends Manager
{

    public function run()
    {
        $topic = $this->get_selected_complex_content_object_item();

        if ($this->get_user()->get_id() == $topic->get_user_id() || $this->get_user()->is_platform_admin() ||
             $this->is_forum_manager($this->get_user()))
        {
            $topic = $this->get_selected_complex_content_object_item();

            $params = array();

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
                $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = Request::get('parent_cloi');
                $params[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            }

            $success = $topic->delete();

            $message = htmlentities(
                Translation::get(
                    ($success ? 'ObjectDeleted' : 'ObjectNotDeleted'),
                    array('OBJECT' => Translation::get('ForumTopic')),
                    Utilities::COMMON_LIBRARIES));

            $this->redirect($message, ($success ? false : true), $params);
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
