<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Twig\TwigFilter;

/**
 * Twig extension for DateTimeUtilities
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DateExtension extends CoreExtension
{
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