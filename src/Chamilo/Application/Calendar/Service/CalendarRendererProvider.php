<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Calendar\Event\EventSource;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider implements
    VisibilitySupport
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Service\VisibilityService
     */
    private $visibilityService;

    /**
     *
     * @var string
     */
    private $visibilityContext;

    /**
     *
     * @param \Chamilo\Application\Calendar\Service\VisibilityService $visibilityService
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters;
     * @param string $visibilityContext
     */
    public function __construct(VisibilityService $visibilityService, $visibilityContext, User $user, $displayParameters)
    {
        $this->visibilityService = $visibilityService;
        $this->visibilityContext = $visibilityContext;

        parent::__construct($user, $displayParameters);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\VisibilityService
     */
    public function getVisibilityService()
    {
        return $this->visibilityService;
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
     * @see \Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport::isSourceVisible()
     */
    public function isSourceVisible(EventSource $source)
    {
        $visibility = $this->getVisibilityService()->findVisibilityBySourceAndUserIdentifier(
            $source->hash(),
            $this->getUser()->getId());
        return ! $visibility instanceof Visibility;
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
        return $this->getSources(self::SOURCE_TYPE_INTERNAL);
    }

    /**
     * Get the external sources
     *
     * @return string[]
     */
    public function getExternalSources()
    {
        return $this->getSources(self::SOURCE_TYPE_EXTERNAL);
    }

    /**
     * Get the sources
     *
     * @return string[]
     */
    public function getAllSources()
    {
        return $this->getSources(self::SOURCE_TYPE_BOTH);
    }

    /**
     *
     * @param integer $sourceType
     * @return string[]
     */
    public function getSources($requestedSourceType)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Manager::package());

        $sources = array();

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $context = $registration[Registration::PROPERTY_CONTEXT];
                $class_name = $context . '\Service\CalendarEventDataProvider';

                if (class_exists($class_name))
                {
                    $reflectionClass = new \ReflectionClass($class_name);
                    if ($reflectionClass->isAbstract())
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
            $sourceNames[] = Translation::get('TypeName', array(), $sourceContext);
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
        return $this->getSourceNames(self::SOURCE_TYPE_INTERNAL);
    }

    /**
     * Get the external source names
     *
     * @return string[]
     */
    public function getExternalSourceNames()
    {
        return $this->getSourceNames(self::SOURCE_TYPE_EXTERNAL);
    }

    /**
     * Get the source names
     *
     * @return string[]
     */
    public function getAllSourceNames()
    {
        return $this->getSourceNames(self::SOURCE_TYPE_BOTH);
    }
}