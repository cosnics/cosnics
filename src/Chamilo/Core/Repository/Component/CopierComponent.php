<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

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
        $selected_content_object_ids = (array) Request :: get(self :: PARAM_CONTENT_OBJECT_ID);

        if (! $selected_content_object_ids)
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        $target_user_id = $this->get_user_id();
        $messages = array();

        foreach ($selected_content_object_ids as $selected_content_object_id)
        {
            $content_object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $selected_content_object_id);

            if (RightsService :: getInstance()->canCopyContentObject(
                $this->get_user(),
                $content_object,
                $this->getWorkspace()))
            {
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
                    if (RightsService :: getInstance()->canCopyContentObject(
                        $this->get_user(),
                        $content_object,
                        $this->getWorkspace()))
                    {
                        $copier = new ContentObjectCopier(
                            $this->get_user(),
                            array($content_object->get_id()),
                            $this->getWorkspace(),
                            $source_user_id,
                            $this->getWorkspace(),
                            $target_user_id,
                            $target_category_id);

                        $copier->run();

                        $messages += $copier->get_messages_for_url();
                    }
                }
            }
        }

        Session :: register(self :: PARAM_MESSAGES, $messages);
        $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS);
        $this->simple_redirect($parameters);
    }
}
