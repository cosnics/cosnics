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
    /**
     * @param int $timestamp
     * @param null $dateFormat
     *
     * @return string
     */
    public function formatDate($timestamp, $dateFormat = null)
    {
        return DatetimeUtilities::getInstance()->formatLocaleDate($dateFormat, $timestamp);
    }

    /**
     * @param int $timestamp
     *
     * @return string
     */
    public function formatLongDate($timestamp)
    {
        return $this->formatDate($timestamp, Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES));
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('formatDate', array($this, 'formatDate')),
            new TwigFilter('formatLongDate', array($this, 'formatLongDate'))
        );
    }
}