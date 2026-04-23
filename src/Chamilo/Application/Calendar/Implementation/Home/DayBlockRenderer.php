<?php
namespace Chamilo\Application\Calendar\Implementation\Home;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Calendar\Service\CalendarTableConfigurationBuilder;
use Chamilo\Libraries\Calendar\Service\View\MiniDayCalendarRenderer;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use IntlDateFormatter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Implementation\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class DayBlockRenderer extends BlockRenderer
{
    public const string CONFIGURATION_HOUR_STEP = 'hour_step';
    public const string CONFIGURATION_TIME_END = 'time_end';
    public const string CONFIGURATION_TIME_HIDE = 'time_hide';
    public const string CONFIGURATION_TIME_START = 'time_start';
    public const string CONTEXT = Manager::CONTEXT;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        protected DatetimeUtilities $datetimeUtilities, protected MiniDayCalendarRenderer $miniDayCalendarRenderer,
        protected ChamiloRequest $request, protected CalendarDataProvider $calendarDataProvider,
        protected CalendarTableConfigurationBuilder $calendarTableConfigurationBuilder
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator);
    }

    /**
     * @throws \TableException
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $events = [];

        $calendarTableConfiguration = $this->calendarTableConfigurationBuilder->buildConfiguration($user);

        if ($user instanceof User) {
            $events = $this->calendarDataProvider->getEvents(
                $user, $this->miniDayCalendarRenderer->getEventsStartTime(
                $calendarTableConfiguration, $this->getDisplayTime()
            ), $this->miniDayCalendarRenderer->getEventsEndTime($calendarTableConfiguration, $this->getDisplayTime())
            );
        }

        return '<div style="max-height: 500px;">' . $this->miniDayCalendarRenderer->renderFullCalendar(
                $calendarTableConfiguration, $events, $this->getDisplayTime()
            ) . '</div>';
    }

    protected function getDisplayTime(): int
    {
        return (int) $this->request->query->get('time', time());
    }

    public function getTitle(Element $block): string
    {
        return $this->datetimeUtilities->formatLocaleDate(
            $this->getDisplayTime(), IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
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
