<?php
namespace Chamilo\Application\Calendar\Implementation\Home;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Implementation\Libraries\CalendarRendererProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interface\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Service\View\MiniDayCalendarRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Service\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DayBlockRenderer extends BlockRenderer implements StaticBlockTitleInterface
{
    public const CONFIGURATION_HOUR_STEP = 'hour_step';
    public const CONFIGURATION_TIME_END = 'time_end';
    public const CONFIGURATION_TIME_HIDE = 'time_hide';
    public const CONFIGURATION_TIME_START = 'time_start';

    public const CONTEXT = \Chamilo\Application\Calendar\Manager::CONTEXT;

    protected VisibilityRepository $calendarRendererProviderRepository;

    protected DatetimeUtilities $datetimeUtilities;

    protected MiniDayCalendarRenderer $miniDayCalendarRenderer;

    protected ChamiloRequest $request;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, DatetimeUtilities $datetimeUtilities,
        MiniDayCalendarRenderer $miniDayCalendarRenderer, ChamiloRequest $request,
        VisibilityRepository $calendarRendererProviderRepository
    )
    {
        parent::__construct(
            $homeService, $urlGenerator, $translator, $configurationConsulter
        );

        $this->datetimeUtilities = $datetimeUtilities;
        $this->miniDayCalendarRenderer = $miniDayCalendarRenderer;
        $this->request = $request;
        $this->calendarRendererProviderRepository = $calendarRendererProviderRepository;
    }

    public function displayContent(Element $block, ?User $user = null): string
    {
        $dataProvider = new CalendarRendererProvider(
            $this->getCalendarRendererProviderRepository(), $user, [], Manager::CONTEXT
        );

        return '<div style="max-height: 500px; overflow: auto;">' .
            $this->getMiniDayCalendarRenderer()->renderFullCalendar($dataProvider, $this->getDisplayTime()) . '</div>';
    }

    public function getCalendarRendererProviderRepository(): VisibilityRepository
    {
        return $this->calendarRendererProviderRepository;
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\Interface\ConfigurableBlockRendererInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables(): array
    {
        return [
            self::CONFIGURATION_HOUR_STEP,
            self::CONFIGURATION_TIME_START,
            self::CONFIGURATION_TIME_END,
            self::CONFIGURATION_TIME_HIDE
        ];
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    protected function getDisplayTime(): int
    {
        return (int) $this->getRequest()->query->get('time', time());
    }

    public function getMiniDayCalendarRenderer(): MiniDayCalendarRenderer
    {
        return $this->miniDayCalendarRenderer;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTitle(Element $block): string
    {
        return $this->getDatetimeUtilities()->formatLocaleDate('%A %d %B %Y', $this->getDisplayTime());
    }

    public function renderContentFooter(): string
    {
        return '</div>';
    }

    public function renderContentHeader(Element $block): string
    {
        return '<div class="portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
    }
}
