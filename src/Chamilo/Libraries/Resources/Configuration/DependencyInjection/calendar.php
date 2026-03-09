<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Calendar\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\Event\EventMiniMonthRenderer;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\DayCalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\TableBuilder\MiniMonthCalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\TableBuilder\MonthCalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\TableBuilder\WeekCalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\View\DayCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\ListCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniDayCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniListCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniMonthCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MonthCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\WeekCalendarRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(HtmlCalendarRendererFactory::class);

    $services->set(EventDayRenderer::class);
    $services->set(EventListRenderer::class);
    $services->set(EventMiniMonthRenderer::class);
    $services->set(EventMonthRenderer::class);

    $services->set(JumpBarRenderer::class);
    $services->set(LegendRenderer::class);

    $services->set(DayCalendarTableBuilder::class)->args([
        '$user' => service('Chamilo\Core\User\CurrentUser'),
        '$defaultHideNonWorkingHours' => '%cosnics.libraries.calendar.hideNonWorkingHours%',
        '$defaultHourStep' => '%cosnics.libraries.calendar.hourStep%',
        '$defaultWorkingHoursEnd' => '%cosnics.libraries.calendar.workingHoursEnd%',
        '$defaultWorkingHoursStart' => '%cosnics.libraries.calendar.workingHoursStart%'
    ]);
    $services->set(MiniMonthCalendarTableBuilder::class)->args([
        '$user' => service('Chamilo\Core\User\CurrentUser'),
        '$defaultFirstDayOfWeek' => '%cosnics.libraries.calendar.firstDayOfWeek%'
    ]);
    $services->set(MonthCalendarTableBuilder::class)->args(
        [
            '$user' => service('Chamilo\Core\User\CurrentUser'),
            '$defaultFirstDayOfWeek' => '%cosnics.libraries.calendar.firstDayOfWeek%'
        ]
    );
    $services->set(WeekCalendarTableBuilder::class)->args(
        [
            '$user' => service('Chamilo\Core\User\CurrentUser'),
            '$defaultFirstDayOfWeek' => '%cosnics.libraries.calendar.firstDayOfWeek%',
            '$defaultHideNonWorkingHours' => '%cosnics.libraries.calendar.hideNonWorkingHours%',
            '$defaultHourStep' => '%cosnics.libraries.calendar.hourStep%',
            '$defaultWorkingHoursEnd' => '%cosnics.libraries.calendar.workingHoursEnd%',
            '$defaultWorkingHoursStart' => '%cosnics.libraries.calendar.workingHoursStart%'
        ]
    );

    $services->set(DayCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(ICalCalendarRenderer::class);
    $services->set(ListCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(MiniDayCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(MiniListCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(MiniMonthCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(MonthCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
    $services->set(WeekCalendarRenderer::class)->tag(HtmlCalendarRenderer::class);
};
