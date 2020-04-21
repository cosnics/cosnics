<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Twig_Extension;
use Twig_SimpleFilter;

/**
 * Twig extension for DateTimeUtilities
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DateExtension extends Twig_Extension
{
    /**
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('formatDate', array($this, 'formatDate')),
            new Twig_SimpleFilter('formatLongDate', array($this, 'formatLongDate'))
        );
    }

    /**
     * @param int $timestamp
     * @param null $dateFormat
     *
     * @return string
     */
    public function formatDate($timestamp, $dateFormat = null)
    {
        return DatetimeUtilities::format_locale_date($dateFormat, $timestamp);
    }

    /**
     * @param int $timestamp
     *
     * @return string
     */
    public function formatLongDate($timestamp)
    {
        return $this->formatDate($timestamp, Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES));
    }
}