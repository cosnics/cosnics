<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.complex_display.forum.component
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumPostDeleterComponent extends Manager
{

    public function run()
    {
        $selected_post_id = Request::get(self::PARAM_SELECTED_FORUM_POST);
        $delete_post = DataManager::retrieve_by_id(ForumPost::class, $selected_post_id);

        if (! $delete_post instanceof ForumPost)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('ForumPost', null, Manager::context()),
                $selected_post_id);
        }

        if ($delete_post->get_user_id() == $this->get_parent()->get_user_id() ||
             $this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $success = $delete_post->delete();
        }
        else
        {
            $success = false;
        }
        $this->my_redirect($success);
    }

    /**
     * redirect
     *
     * @param type $success
     */
    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectDeleted' : 'ObjectNotdeleted'),
                array('OBJECT' => Translation::get('ForumPost')),
                StringUtilities::LIBRARIES));

        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_TOPIC;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $filter = array(self::PARAM_SELECTED_FORUM_POST);

        $this->redirectWithMessage($message, !$success, $params, $filter);
    }
}
