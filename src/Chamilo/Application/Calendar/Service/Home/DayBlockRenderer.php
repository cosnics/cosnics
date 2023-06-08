<?php
namespace Chamilo\Application\Calendar\Service\Home;

use Chamilo\Application\Calendar\Ajax\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
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
class DayBlockRenderer extends BlockRenderer implements ConfigurableBlockInterface, StaticBlockTitleInterface
{
    public const CONFIGURATION_HOUR_STEP = 'hour_step';
    public const CONFIGURATION_TIME_END = 'time_end';
    public const CONFIGURATION_TIME_HIDE = 'time_hide';
    public const CONFIGURATION_TIME_START = 'time_start';

    protected DatetimeUtilities $datetimeUtilities;

    protected MiniDayCalendarRenderer $miniDayCalendarRenderer;

    protected ChamiloRequest $request;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, DatetimeUtilities $datetimeUtilities,
        MiniDayCalendarRenderer $miniDayCalendarRenderer, ChamiloRequest $request,
        ElementRightsService $elementRightsService
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService);

        $this->datetimeUtilities = $datetimeUtilities;
        $this->miniDayCalendarRenderer = $miniDayCalendarRenderer;
        $this->request = $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function displayContent(Block $block, ?User $user = null): string
    {
        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(), $user, [], Manager::CONTEXT
        );

        return '<div style="max-height: 500px; overflow: auto;">' .
            $this->getMiniDayCalendarRenderer()->renderFullCalendar($dataProvider, $this->getDisplayTime()) . '</div>';
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface::getConfigurationVariables()
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

    public function getTitle(Block $block, ?User $user = null): string
    {
        return $this->getDatetimeUtilities()->formatLocaleDate('%A %d %B %Y', $this->getDisplayTime());
    }

    public function renderContentFooter(Block $block): string
    {
        return '</div>';
    }

    public function renderContentHeader(Block $block): string
    {
        return '<div class="portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
    }
}
