<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension for DateTimeUtilities
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DateExtension extends AbstractExtension
{

    public function formatDate(int $timestamp, ?string $dateFormat = null): string
    {
        return DatetimeUtilities::getInstance()->formatLocaleDate($dateFormat, $timestamp);
    }

    public function formatLongDate(int $timestamp): string
    {
        return $this->formatDate($timestamp, Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES));
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('formatDate', [$this, 'formatDate']),
            new TwigFilter('formatLongDate', [$this, 'formatLongDate'])
        ];
    }
}