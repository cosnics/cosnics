<?php

namespace Chamilo\Libraries\Calendar\Architecture\Enum;

use Chamilo\Libraries\Calendar\Service\View\DayCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\ListCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniDayCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniListCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniMonthCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MonthCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\WeekCalendarRenderer;

enum HtmlCalendarRendererTypeEnum: string
{
    case DAY = 'Day';
    case LIST = 'List';
    case MONTH = 'Month';
    case WEEK = 'Week';
    case MINI_DAY = 'MiniDay';
    case MINI_LIST = 'MiniList';
    case MINI_MONTH = 'MiniMonth';

    public static function getType(string $className): HtmlCalendarRendererTypeEnum
    {
        return match ($className) {
            DayCalendarRenderer::class => self::DAY,
            ListCalendarRenderer::class => self::LIST,
            MiniDayCalendarRenderer::class => self::MINI_DAY,
            MiniListCalendarRenderer::class => self::MINI_LIST,
            MiniMonthCalendarRenderer::class => self::MINI_MONTH,
            MonthCalendarRenderer::class => self::MONTH,
            WeekCalendarRenderer::class => self::WEEK
        };
    }

    public static function getTypeValue(string $className): string
    {
        return self::getType($className)->value;
    }
}
