<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: forum_subforum_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.forum.component
 */
class ForumSubforumDeleterComponent extends Manager
{

    public function run()
    {
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {

            $subforum = $this->get_selected_complex_content_object_item();

            $params = array();

            if ($subforum->get_parent_object() == $this->get_root_content_object())
            {
                $params[self :: PARAM_ACTION] = self :: ACTION_VIEW_FORUM;
                $params[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
                $params[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            }
            else
            {
                $params[self :: PARAM_ACTION] = self :: ACTION_VIEW_FORUM;
                $params[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = Request :: get('parent_cloi');
            }
            $params[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $success = $subforum->delete();

            $message = htmlentities(
                Translation :: get(
                    ($success ? 'ObjectDeleted' : 'ObjectNotDeleted'),
                    array('OBJECT' => Translation :: get('Subforum')),
                    Utilities :: COMMON_LIBRARIES));

            $this->redirect($message, ($success ? false : true), $params);
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
