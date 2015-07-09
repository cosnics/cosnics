<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\CalendarContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;

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
     * @var \Chamilo\Application\Weblcms\Renderer\PublicationList\Type\CalendarContentObjectPublicationListRenderer
     */
    private $renderer;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $dataUser;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $viewingUser;

    /**
     *
     * @var string[]
     */
    private $displayParameters;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Renderer\PublicationList\Type\CalendarContentObjectPublicationListRenderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters;
     */
    public function __construct(CalendarContentObjectPublicationListRenderer $renderer, User $dataUser,
        User $viewingUser, $displayParameters)
    {
        $this->renderer = $renderer;
        $this->dataUser = $dataUser;
        $this->viewingUser = $viewingUser;
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Renderer\PublicationList\Type\CalendarContentObjectPublicationListRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Renderer\PublicationList\Type\CalendarContentObjectPublicationListRenderer $renderer
     */
    public function setRenderer(CalendarContentObjectPublicationListRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getDataUser()
    {
        return $this->dataUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     */
    public function setDataUser(User $dataUser)
    {
        $this->dataUser = $dataUser;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getViewingUser()
    {
        return $this->viewingUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     */
    public function setViewingUser(User $viewingUser)
    {
        $this->viewingUser = $viewingUser;
    }

    /**
     *
     * @return string[]
     */
    public function getDisplayParameters()
    {
        return $this->displayParameters;
    }

    /**
     *
     * @param string[] $displayParameters
     */
    public function setDisplayParameters($displayParameters)
    {
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport::getEventActions()
     */
    public function getEventActions($event)
    {
        $actions = array();

        if ($event->get_context() == __NAMESPACE__)
        {
            $actions[] = new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->getRenderer()->get_publication_editing_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON);

            $actions[] = new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->getRenderer()->get_publication_deleting_url($event->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true);
        }

        return $actions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $startTime
     * @param integer $endTime
     */
    public function aggregateEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime, $endTime)
    {
        $publications = $this->getRenderer()->get_publications();
        $events = array();

        foreach ($publications as $publication)
        {
            if (method_exists(
                $this->getRenderer()->get_tool_browser()->get_parent(),
                'convert_content_object_publication_to_calendar_event'))
            {
                $object = $this->getRenderer()->get_tool_browser()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication,
                    $startTime,
                    $endTime);
            }
            else
            {
                $object = $this->getRenderer()->get_content_object_from_publication($publication);
            }

            $publicationObject = new ContentObjectPublication();
            $publicationObject->set_default_properties($publication);

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