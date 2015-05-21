<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Renderer\Event;

use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package application\weblcms\integration\libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventListRenderer extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\EventListRenderer
{

    /**
     *
     * @see \core\repository\integration\libraries\calendar\renderer\EventListRenderer::get_attachment_link()
     */
    function get_attachment_link(Event $event, ContentObject $attachment)
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => $event->get_context(),
                Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $event->get_course_id(),
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                    \Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager :: context()),
                \Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager :: ACTION_VIEW_ATTACHMENT,
                \Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager :: PARAM_PUBLICATION_ID => $event->get_id(),
                'object_id' => $attachment->get_id()));
        return $redirect->getUrl();
    }
}
