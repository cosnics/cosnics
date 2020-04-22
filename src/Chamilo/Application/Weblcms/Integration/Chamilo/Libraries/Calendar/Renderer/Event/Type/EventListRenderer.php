<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventListRenderer extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer
{

    /**
     *
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer::getAttachmentLink()
     */
    function getAttachmentLink(Event $event, ContentObject $attachment)
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => $event->getContext(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE, 
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $event->getCourseId(), 
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                    Manager::context()),
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_ATTACHMENT,
                Manager::PARAM_PUBLICATION_ID => $event->getId(),
                'object_id' => $attachment->get_id()));
        return $redirect->getUrl();
    }
}
