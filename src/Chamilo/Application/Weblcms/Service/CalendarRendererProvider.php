<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider implements
    \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport
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
     * @param string[] $displayParameters;
     */
    public function __construct(Application $renderer, User $dataUser, User $viewingUser, $displayParameters)
    {
        $this->renderer = $renderer;

        parent::__construct($dataUser, $viewingUser, $displayParameters);
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
     * @see \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport::getEventActions()
     */
    public function getEventActions($event)
    {
        $actions = array();

        if ($event->getContext() == \Chamilo\Application\Weblcms\Manager::package())
        {
            $actions[] = new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Edit'),
                $this->getRenderer()->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $event->getId())),
                ToolbarItem::DISPLAY_ICON);

            $actions[] = new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Delete'),
                $this->getRenderer()->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $event->getId())),
                ToolbarItem::DISPLAY_ICON,
                true);
        }

        return $actions;
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
        $events = array();

        foreach ($publications as $publication)
        {

            if (method_exists(
                $this->getRenderer()->get_parent(),
                'convert_content_object_publication_to_calendar_event'))
            {
                $object = $this->getRenderer()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication,
                    $startTime,
                    $endTime);
            }
            else
            {
                $class = $publication[ContentObject::PROPERTY_TYPE];
                $object = new $class($publication);
                $object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            }

            $publicationObject = new ContentObjectPublication();
            $publicationObject->set_default_properties($publication);
            $publicationObject->set_content_object($object);

            $eventParser = new EventParser($publicationObject, $startTime, $endTime);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = array(), $filterParameters = array(), $encodeEntities = false)
    {
        $redirect = new Redirect($parameters, $filterParameters, $encodeEntities);
        return $redirect->getUrl();
    }

    /**
     *
     * @return boolean
     */
    public function supportsVisibility()
    {
        return $this instanceof VisibilitySupport;
    }

    /**
     *
     * @return boolean
     */
    public function supportsActions()
    {
        return $this instanceof ActionSupport;
    }
}