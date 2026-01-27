<?php
namespace Chamilo\Application\Calendar\Implementation\Libraries;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Interface\VisibilitySupport;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;

/**
 * @package Chamilo\Application\Calendar\Implementation\Libraries
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
    implements VisibilitySupport
{

    private VisibilityRepository $dataProviderRepository;

    /**
     * @var \Chamilo\Application\Calendar\Storage\DataClass\Visibility[]
     */
    private array $visibilities;

    private string $visibilityContext;

    /**
     * @param string[] $displayParameters ;
     */
    public function __construct(
        VisibilityRepository $dataProviderRepository, User $dataUser, array $displayParameters,
        string $visibilityContext
    )
    {
        $this->dataProviderRepository = $dataProviderRepository;
        $this->visibilityContext = $visibilityContext;

        parent::__construct($dataUser, $displayParameters);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
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

    public function getCalendarRendererProviderRepository(): VisibilityRepository
    {
        return $this->dataProviderRepository;
    }

    /**
     * @return string[]
     */
    public function getSourceNames(): array
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

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Visibility[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getVisibilities($userIdentifier): array
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

    public function getVisibilityData(): array
    {
        return [];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function isSourceVisible(string $source, ?int $userIdentifier = null): bool
    {
        if (is_null($userIdentifier))
        {
            $userIdentifier = $this->getDataUser()->getId();
        }

        return !array_key_exists($source, $this->getVisibilities($userIdentifier));
    }
}