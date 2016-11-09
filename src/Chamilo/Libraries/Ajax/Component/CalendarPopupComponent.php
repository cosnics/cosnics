<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarPopupComponent extends \Chamilo\Libraries\Ajax\Manager implements NoAuthenticationSupport
{

    public function run()
    {
        $html = array();

        Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);

        $DaysShort = array(
            Translation :: get("MondayShort", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("TuesdayShort", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("WednesdayShort", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("ThursdayShort", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("FridayShort", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("SaturdayShort", null, Utilities :: COMMON_LIBRARIES));

        // Defining the days of the week to allow translation of the days
        $DaysLong = array(
            Translation :: get("MondayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("TuesdayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("WednesdayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("ThursdayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("FridayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("SaturdayLong", null, Utilities :: COMMON_LIBRARIES));

        $start_of_week = PlatformSetting :: get('first_day_of_week', 'Chamilo\Libraries\Calendar');
        if ($start_of_week == 'sunday')
        {
            array_unshift($DaysShort, Translation :: get("SundayShort", null, Utilities :: COMMON_LIBRARIES));
            array_unshift($DaysLong, Translation :: get("SundayLong", null, Utilities :: COMMON_LIBRARIES));

            $start_of_week_identifier = 1;
        }
        else
        {
            $DaysShort[] = Translation :: get("SundayShort", null, Utilities :: COMMON_LIBRARIES);
            $DaysLong[] = Translation :: get("SundayLong", null, Utilities :: COMMON_LIBRARIES);

            $start_of_week_identifier = 0;
        }
        // Defining the months of the year to allow translation of the months
        $MonthsLong = array(
            Translation :: get("JanuaryLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("FebruaryLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("MarchLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("AprilLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("MayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("JuneLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("JulyLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("AugustLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("SeptemberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("OctoberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("NovemberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("DecemberLong", null, Utilities :: COMMON_LIBRARIES));

        $html[] = $this->render_header();

        $html[] = ResourceManager :: getInstance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries\Format', true) . 'TblChange.js');

        $html[] = '<script type="text/javascript">';
        $html[] = '/* <![CDATA[ */';
        $html[] = 'var month_names = new Array(';

        foreach ($MonthsLong as $index => $month)
        {
            $html[] = '"' . $month . '",';
        }
        $html[] = '"");';

        $html[] = 'var day_names = new Array(';

        foreach ($DaysShort as $index => $day)
        {
            $html[] = '"' . $day . '",';
        }
        $html[] = '"");';
        $html[] = '/* ]]> */';
        $html[] = '</script>';

        $html[] = '<div id="calendar_data"></div>';
        $html[] = '<div id="clock_data"></div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'initCalendar(' . $start_of_week_identifier . ');';
        $html[] = '</script>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}