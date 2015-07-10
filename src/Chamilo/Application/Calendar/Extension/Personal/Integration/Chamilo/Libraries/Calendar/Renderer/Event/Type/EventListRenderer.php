<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventListRenderer extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer
{

    /**
     *
     * @see \core\repository\integration\libraries\calendar\renderer\EventListRenderer::get_attachment_link()
     */
    function get_attachment_link(Event $event, ContentObject $attachment)
    {
        $parameters = array(
            Application :: PARAM_CONTEXT => $event->get_context(),
            Application :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_VIEW_ATTACHMENT,
            \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID => $event->get_id(),
            \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_OBJECT => $attachment->get_id());

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}