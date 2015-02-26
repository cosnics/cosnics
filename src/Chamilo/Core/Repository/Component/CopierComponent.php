<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: content_object_copier.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class CopierComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $selected_content_object_ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);

        if (! $selected_content_object_ids)
        {
            return Display :: error_page(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
        }

        $target_user_id = $this->get_user_id();
        $messages = array();

        foreach ($selected_content_object_ids as $selected_content_object_id)
        {
            $content_object = DataManager :: retrieve_content_object($selected_content_object_id);
            $source_user_id = $content_object->get_owner_id();

            if ($target_user_id != $content_object->get_owner_id())
            {
                $target_category_id = 0;
            }
            else
            {
                $target_category_id = $content_object->get_parent_id();
            }

            if ($content_object instanceof ContentObject)
            {
                $is_owner = $content_object->get_owner_id() == $this->get_user_id();
                $has_copy_right = RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
                    RepositoryRights :: COPY_RIGHT,
                    $content_object->get_id(),
                    RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                    $content_object->get_owner_id());

                // check for copy right
                if ($is_owner || $has_copy_right)
                {
                    $copier = new ContentObjectCopier(
                        array($content_object->get_id()),
                        $source_user_id,
                        $target_user_id,
                        $target_category_id);
                    $copier->run();
                    $messages += $copier->get_messages_for_url();
                }
            }
        }

        Session :: register(self :: PARAM_MESSAGES, $messages);
        $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS);
        $this->simple_redirect($parameters);
    }
}
