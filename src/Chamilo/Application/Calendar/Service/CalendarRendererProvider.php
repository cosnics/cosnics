<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use ReflectionClass;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
    implements VisibilitySupport, ActionSupport
{

    /**
     * @var \Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository
     */
    private $dataProviderRepository;

    /**
     * @var \Chamilo\Application\Calendar\Storage\DataClass\Visibility[]
     */
    private $visibilities;

    /**
     * @var string
     */
    private $visibilityContext;

    /**
     * @param \Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param string[] $displayParameters ;
     * @param string $visibilityContext
     */
    public function __construct(
        CalendarRendererProviderRepository $dataProviderRepository, User $dataUser, $displayParameters,
        $visibilityContext
    )
    {
        $this->dataProviderRepository = $dataProviderRepository;
        $this->visibilityContext = $visibilityContext;

        parent::__construct($dataUser, $displayParameters);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \ReflectionException
     */
    public function aggregateEvents(?int $startTime = null, ?int $endTime = null): array
    {
        $events = [];

        foreach ($this->getSources() as $implementor)
        {
            $implementorEvents = $implementor->getEvents($this, $startTime, $endTime);

            $events = array_merge($events, $implementorEvents);
        }

        return $events;
    }

    /**
     * @return \Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository
     */
    public function getCalendarRendererProviderRepository()
    {
        return $this->dataProviderRepository;
    }

    /**
     * @see \Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport::getEventActions()
     */
    public function getEventActions(Event $event): array
    {
        $actions = [];

        if ($event->getContext() == \Chamilo\Application\Calendar\Extension\Personal\Manager::CONTEXT)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getPublicationEditingUrl($event->getId()), ToolbarItem::DISPLAY_ICON
            );

            $actions[] = new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->getPublicationDeletingUrl($event->getId()), ToolbarItem::DISPLAY_ICON, true
            );
        }

        return $actions;
    }

    /**
     * @param int $eventIdentifier
     *
     * @return string
     */
    private function getPublicationDeletingUrl($eventIdentifier)
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Personal\Manager::CONTEXT,
                \Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager::ACTION_DELETE,
                \Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_PUBLICATION_ID => $eventIdentifier
            ]
        );
    }

    /**
     * @param int $eventIdentifier
     *
     * @return string
     */
    private function getPublicationEditingUrl($eventIdentifier)
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Personal\Manager::CONTEXT,
                \Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Personal\Manager::ACTION_EDIT,
                \Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_PUBLICATION_ID => $eventIdentifier
            ]
        );
    }

    public function getSourceNames()
    {
        $sourceNames = [];

        foreach ($this->getSources() as $sourceContext => $sourceImplementor)
        {
            $sourceNames[] = Translation::get('TypeName', [], $sourceContext);
        }

        sort($sourceNames);

        return $sourceNames;
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\CalendarInterface[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getSources()
    {
        /**
         * @var \Chamilo\Configuration\Service\Consulter\RegistrationConsulter $registrationConsulter
         */
        $registrationConsulter =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(RegistrationConsulter::class);

        $registrations = $registrationConsulter->getIntegrationRegistrations(Manager::CONTEXT);

        $sources = [];

        foreach ($registrations as $registration)
        {

            if ($registration[Registration::PROPERTY_STATUS])
            {
                $context = $registration[Registration::PROPERTY_CONTEXT];
                $class_name = $context . '\Service\CalendarEventDataProvider';

                if (class_exists($class_name))
                {
                    $reflectionClass = new ReflectionClass($class_name);
                    if ($reflectionClass->isAbstract())
                    {
                        continue;
                    }

                    $implementor = new $class_name();

                    $sources[$context] = $implementor;
                }
            }
        }

        return $sources;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    public function getVisibilities($userIdentifier)
    {
        if (!isset($this->visibilities))
        {
            $visibilities = $this->getCalendarRendererProviderRepository()->findVisibilitiesByUserIdentifier(
                $userIdentifier
            );

            $this->visibilities = [];

            foreach ($visibilities as $visibility)
            {
                $this->visibilities[$visibility->getSource()] = $visibility;
            }
        }

        return $this->visibilities;
    }

    public function getVisibilityContext(): string
    {
        return $this->visibilityContext;
    }

    public function getVisibilityData(): array
    {
        return [];
    }

    public function isSourceVisible(string $source, ?int $userIdentifier = null): bool
    {
        if (is_null($userIdentifier))
        {
            $userIdentifier = $this->getDataUser()->getId();
        }

        return !array_key_exists($source, $this->getVisibilities($userIdentifier));
    }

    /**
     * @param \Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     */
    public function setCalendarRendererProviderRepository(CalendarRendererProviderRepository $dataProviderRepository)
    {
        $this->dataProviderRepository = $dataProviderRepository;
    }

    /**
     * @param string $visibilityContext
     */
    public function setVisibilityContext($visibilityContext)
    {
        $this->visibilityContext = $visibilityContext;
    }
}