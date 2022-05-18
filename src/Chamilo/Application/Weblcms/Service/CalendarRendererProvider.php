<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider
    implements ActionSupport
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    private $renderer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters ;
     */
    public function __construct(Application $renderer, User $dataUser, User $viewingUser, $displayParameters)
    {
        $this->renderer = $renderer;

        parent::__construct($dataUser, $viewingUser, $displayParameters);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     */
    public function aggregateEvents($sourceType, $startTime, $endTime)
    {
        $publications = $this->getRenderer()->get_publications();
        $events = [];

        foreach ($publications as $publication)
        {

            if (method_exists(
                $this->getRenderer()->get_parent(), 'convert_content_object_publication_to_calendar_event'
            ))
            {
                $object = $this->getRenderer()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication, $startTime, $endTime
                );
            }
            else
            {
                $class = $publication[ContentObject::PROPERTY_TYPE];
                $object = new $class($publication);
                $object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            }

            $publicationObject = new ContentObjectPublication();
            $publicationObject->setDefaultProperties($publication);
            $publicationObject->set_content_object($object);

            $eventParser = new EventParser($publicationObject, $startTime, $endTime);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport::getEventActions()
     */
    public function getEventActions($event)
    {
        $actions = [];

        if ($event->getContext() == \Chamilo\Application\Weblcms\Manager::package())
        {
            $actions[] = new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getRenderer()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE_PUBLICATION,
                        Manager::PARAM_PUBLICATION_ID => $event->getId()
                    )
                ), ToolbarItem::DISPLAY_ICON
            );

            $actions[] = new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                $this->getRenderer()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_PUBLICATION_ID => $event->getId()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
            );
        }

        return $actions;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     */
    public function setRenderer(BrowserComponent $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = [], $filterParameters = [], $encodeEntities = false)
    {
        $redirect = new Redirect($parameters, $filterParameters, $encodeEntities);

        return $redirect->getUrl();
    }
}