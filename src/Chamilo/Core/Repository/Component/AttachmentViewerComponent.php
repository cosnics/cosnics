<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: attachment_viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        $object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        $attachment_id = Request :: get(self :: PARAM_ATTACHMENT_ID);

        if ($object_id && $attachment_id)
        {
            $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($object_id);
            $repo_rights = RepositoryRights :: get_instance();

            if ($object->get_owner_id() != $this->get_user_id() && ! $repo_rights->is_allowed_in_user_subtree(
                RepositoryRights :: VIEW_RIGHT,
                $object_id,
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                $object->get_owner_id()))
            {
                throw new NotAllowedException();
            }

            $attachment = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($attachment_id);

            $html = array();

            if ($object->is_attached_to_or_included_in($attachment_id))
            {
                $html[] = Display :: small_header();

                $html[] = ContentObjectRenditionImplementation :: launch(
                    $attachment,
                    ContentObjectRendition :: FORMAT_HTML,
                    ContentObjectRendition :: VIEW_FULL,
                    $this);
                $html[] = Display :: small_footer();
            }
            else
            {
                $html[] = Display :: small_header();
                $html[] = $this->display_error_message('WhatsUpDoc', null, Utilities :: COMMON_LIBRARIES);
                $html[] = Display :: small_footer();
            }

            return implode(PHP_EOL, $html);
        }
        else
        {
            $html = array();

            $html[] = Display :: small_header();
            $html[] = $this->display_error_message('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES);
            $html[] = Display :: small_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID);
    }
}
