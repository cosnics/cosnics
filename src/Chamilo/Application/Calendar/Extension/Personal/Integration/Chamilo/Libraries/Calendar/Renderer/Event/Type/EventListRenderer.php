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
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer::getAttachmentLink()
     */
    function getAttachmentLink(Event $event, ContentObject $attachment)
    {
        $parameters = array(
            Application :: PARAM_CONTEXT => $event->getContext(),
            Application :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_VIEW_ATTACHMENT,
            \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID => $event->getId(),
            \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_OBJECT => $attachment->get_id());

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}