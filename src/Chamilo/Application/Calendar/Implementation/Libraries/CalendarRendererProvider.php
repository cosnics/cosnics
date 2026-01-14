<?php
namespace Chamilo\Application\Calendar\Implementation\Libraries;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilitySupport;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
    implements VisibilitySupport
{

    /**
     * @var \Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository
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
     * @param \Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param string[] $displayParameters ;
     * @param string $visibilityContext
     */
    public function __construct(
        VisibilityRepository $dataProviderRepository, User $dataUser, $displayParameters, $visibilityContext
    )
    {
        $this->dataProviderRepository = $dataProviderRepository;
        $this->visibilityContext = $visibilityContext;

        parent::__construct($dataUser, $displayParameters);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function aggregateEvents(?int $startTime = null, ?int $endTime = null): array
    {
        $events = [];

        foreach ($this->getCalendarProviders() as $calendarDataProviders)
        {
            $implementorEvents = $calendarDataProviders->getEvents($this, $startTime, $endTime);

            $events = array_merge($events, $implementorEvents);
        }

        return $events;
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface[]
     */
    public function getCalendarProviders(): array
    {
        /**
         * @var \Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection $calendarProvider
         */
        $calendarProvider = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            CalendarExtensionDataProviderCollection::class
        );

        return $calendarProvider->getCalendarExtensionDataProviders();
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository
     */
    public function getCalendarRendererProviderRepository()
    {
        return $this->dataProviderRepository;
    }

    public function getSourceNames()
    {
        $sourceNames = [];

        foreach ($this->getCalendarProviders() as $calenderDataProvider)
        {
            $sourceNames[] = $calenderDataProvider->getName();
        }

        sort($sourceNames);

        return $sourceNames;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    public function getVisibilities($userIdentifier)
    {
        if (!isset($this->visibilities))
        {
            $visibilities = $this->getCalendarRendererProviderRepository()->retrieveVisibilitiesByUserIdentifier(
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

    /**
     * @param string $visibilityContext
     */
    public function setVisibilityContext($visibilityContext)
    {
        $this->visibilityContext = $visibilityContext;
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
     * @param \Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository $dataProviderRepository
     */
    public function setCalendarRendererProviderRepository(VisibilityRepository $dataProviderRepository)
    {
        $this->dataProviderRepository = $dataProviderRepository;
    }
}