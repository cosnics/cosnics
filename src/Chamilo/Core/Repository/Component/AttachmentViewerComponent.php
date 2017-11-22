<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.repository_manager.component
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        $object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $object_id);

        $attachment_id = Request::get(self::PARAM_ATTACHMENT_ID);

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        if ($object_id && $attachment_id)
        {
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $object_id);

            if (! RightsService::getInstance()->canViewContentObject($this->get_user(), $object, $this->getWorkspace()))
            {
                throw new NotAllowedException();
            }

            $attachment = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $attachment_id);

            $html = array();

            if ($object->is_attached_to_or_included_in($attachment_id))
            {
                $html[] = $this->render_header();

                $html[] = ContentObjectRenditionImplementation::launch(
                    $attachment,
                    ContentObjectRendition::FORMAT_HTML,
                    ContentObjectRendition::VIEW_FULL,
                    $this);
                $html[] = $this->render_footer();
            }
            else
            {
                $html[] = $this->render_header();
                $html[] = $this->display_error_message('WhatsUpDoc', null, Utilities::COMMON_LIBRARIES);
                $html[] = $this->render_footer();
            }

            return implode(PHP_EOL, $html);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message('NoObjectSelected', null, Utilities::COMMON_LIBRARIES);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
