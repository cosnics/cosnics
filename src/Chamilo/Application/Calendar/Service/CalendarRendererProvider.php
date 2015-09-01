<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider implements
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport,
    \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository
     */
    private $dataProviderRepository;

    /**
     *
     * @var string
     */
    private $visibilityContext;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters;
     * @param string $visibilityContext
     */
    public function __construct(CalendarRendererProviderRepository $dataProviderRepository, User $dataUser,
        User $viewingUser, $displayParameters, $visibilityContext)
    {
        $this->dataProviderRepository = $dataProviderRepository;
        $this->visibilityContext = $visibilityContext;

        parent :: __construct($dataUser, $viewingUser, $displayParameters);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository
     */
    public function getCalendarRendererProviderRepository()
    {
        return $this->dataProviderRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     */
    public function setCalendarRendererProviderRepository(CalendarRendererProviderRepository $dataProviderRepository)
    {
        $this->dataProviderRepository = $dataProviderRepository;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport::getVisibilityContext()
     */
    public function getVisibilityContext()
    {
        return $this->visibilityContext;
    }

    /**
     *
     * @param string $visibilityContext
     */
    public function setVisibilityContext($visibilityContext)
    {
        $this->visibilityContext = $visibilityContext;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport::getVisibilityData()
     */
    public function getVisibilityData()
    {
        return array();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport::isSourceVisible()
     */
    public function isSourceVisible($source, $userIdentifier = null)
    {
        if (is_null($userIdentifier))
        {
            $userIdentifier = $this->getViewingUser()->getId();
        }

        $visibility = $this->getCalendarRendererProviderRepository()->findVisibilityBySourceAndUserIdentifier(
            $source,
            $userIdentifier);
        return ! $visibility instanceof Visibility;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport::getEventActions()
     */
    public function getEventActions($event)
    {
        $actions = array();

        if ($event->getContext() == \Chamilo\Application\Calendar\Extension\Personal\Manager :: context())
        {
            $actions[] = new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->getPublicationEditingUrl($event->getId()),
                ToolbarItem :: DISPLAY_ICON);

            $actions[] = new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->getPublicationDeletingUrl($event->getId()),
                ToolbarItem :: DISPLAY_ICON,
                true);
        }

        return $actions;
    }

    /**
     *
     * @param integer $eventIdentifier
     * @return string
     */
    private function getPublicationEditingUrl($eventIdentifier)
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Personal\Manager :: context(),
                \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_EDIT,
                \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID => $eventIdentifier));

        return $redirect->getUrl();
    }

    /**
     *
     * @param integer $eventIdentifier
     * @return string
     */
    private function getPublicationDeletingUrl($eventIdentifier)
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Personal\Manager :: context(),
                \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_DELETE,
                \Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID => $eventIdentifier));

        return $redirect->getUrl();
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param int $sourceType
     * @param integer $startTime
     * @param integer $endTime
     */
    public function aggregateEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $requestedSourceType,
        $startTime, $endTime)
    {
        $events = array();

        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(
            \Chamilo\Application\Calendar\Manager :: context());

        foreach ($registrations as $registration)
        {
            if ($registration->is_active())
            {
                $context = $registration->get_context();
                $class_name = $context . '\Manager';

                if (class_exists($class_name))
                {
                    $implementor = new $class_name();

                    if ($this->matchesRequestedSource($requestedSourceType, $implementor->getSourceType()))
                    {
                        $events = array_merge(
                            $events,
                            $implementor->getEvents($renderer, $requestedSourceType, $startTime, $endTime));
                    }
                }
            }
        }

        return $events;
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