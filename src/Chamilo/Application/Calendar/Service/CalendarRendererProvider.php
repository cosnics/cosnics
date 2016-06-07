<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     * @param int $sourceType
     * @param integer $startTime
     * @param integer $endTime
     */
    public function aggregateEvents($requestedSourceType, $startTime, $endTime)
    {
        $events = array();
        
        foreach ($this->getSources($requestedSourceType) as $context => $implementor)
        {
            $events = array_merge($events, $implementor->getEvents($this, $requestedSourceType, $startTime, $endTime));
        }
        
        return $events;
    }

    /**
     * Get the internal sources
     * 
     * @return string[]
     */
    public function getInternalSources()
    {
        return $this->getSources(self :: SOURCE_TYPE_INTERNAL);
    }

    /**
     * Get the external sources
     * 
     * @return string[]
     */
    public function getExternalSources()
    {
        return $this->getSources(self :: SOURCE_TYPE_EXTERNAL);
    }

    /**
     * Get the sources
     * 
     * @return string[]
     */
    public function getAllSources()
    {
        return $this->getSources(self :: SOURCE_TYPE_BOTH);
    }

    /**
     *
     * @param integer $sourceType
     * @return string[]
     */
    public function getSources($requestedSourceType)
    {
        $registrations = Configuration :: get_instance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Manager :: package());
        
        $sources = array();
        
        foreach ($registrations as $registration)
        {
            if ($registration[Registration :: PROPERTY_STATUS])
            {
                $context = $registration[Registration :: PROPERTY_CONTEXT];
                $class_name = $context . '\Service\CalendarEventDataProvider';
                
                if (class_exists($class_name))
                {
                    $reflectionClass = new \ReflectionClass($class_name);
                    if($reflectionClass->isAbstract())
                    {
                        continue;
                    }
                    
                    $implementor = new $class_name();
                    
                    if ($this->matchesRequestedSource($requestedSourceType, $implementor->getSourceType()))
                    {
                        $sources[$context] = $implementor;
                    }
                }
            }
        }
        
        return $sources;
    }

    public function getSourceNames($requestedSourceType)
    {
        $sourceNames = array();
        
        foreach ($this->getSources($requestedSourceType) as $sourceContext => $sourceImplementor)
        {
            $sourceNames[] = Translation :: get('TypeName', array(), $sourceContext);
        }
        
        sort($sourceNames);
        
        return $sourceNames;
    }

    /**
     * Get the internal source names
     * 
     * @return string[]
     */
    public function getInternalSourceNames()
    {
        return $this->getSourceNames(self :: SOURCE_TYPE_INTERNAL);
    }

    /**
     * Get the external source names
     * 
     * @return string[]
     */
    public function getExternalSourceNames()
    {
        return $this->getSourceNames(self :: SOURCE_TYPE_EXTERNAL);
    }

    /**
     * Get the source names
     * 
     * @return string[]
     */
    public function getAllSourceNames()
    {
        return $this->getSourceNames(self :: SOURCE_TYPE_BOTH);
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