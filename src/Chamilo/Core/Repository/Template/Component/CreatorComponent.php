<?php
namespace Chamilo\Core\Repository\Template\Component;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Template\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * Repository manager component which provides functionality to create a template based on another content object
 *
 * @package repository.lib.repository_manager.component
 * @author Hans De Bisschop
 */
class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $selected_content_object_ids = Request :: get(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);

        if (! $selected_content_object_ids)
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        $messages = array();

        foreach ($selected_content_object_ids as $selected_content_object_id)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $selected_content_object_id);
            $source_user_id = $content_object->get_owner_id();

            if ($content_object instanceof ContentObject)
            {
                $is_owner = $content_object->get_owner_id() == $this->get_user_id();

                // check for copy right
                if ($is_owner)
                {
                    $copier = new ContentObjectCopier(array($content_object->get_id()), $source_user_id, 0, 0);
                    $copier->run();
                    $messages += $copier->get_messages_for_url();
                }
            }
        }

        Session :: register(self :: PARAM_MESSAGES, $messages);
        $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE);
        $this->simple_redirect($parameters);
    }
}
