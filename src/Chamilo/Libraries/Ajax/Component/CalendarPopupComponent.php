<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarPopupComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $translator = $this->getTranslator();

        $html = array();

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $DaysShort = array(
            $translator->trans("MondayShort", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("TuesdayShort", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("WednesdayShort", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("ThursdayShort", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("FridayShort", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("SaturdayShort", array(), Utilities::COMMON_LIBRARIES)
        );

        // Defining the days of the week to allow translation of the days
        $DaysLong = array(
            $translator->trans("MondayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("TuesdayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("WednesdayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("ThursdayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("FridayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("SaturdayLong", array(), Utilities::COMMON_LIBRARIES)
        );

        $start_of_week =
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));
        
        if ($start_of_week == 'sunday')
        {
            array_unshift($DaysShort, $translator->trans("SundayShort", array(), Utilities::COMMON_LIBRARIES));
            array_unshift($DaysLong, $translator->trans("SundayLong", array(), Utilities::COMMON_LIBRARIES));

            $start_of_week_identifier = 1;
        }
        else
        {
            $DaysShort[] = $translator->trans("SundayShort", array(), Utilities::COMMON_LIBRARIES);
            $DaysLong[] = $translator->trans("SundayLong", array(), Utilities::COMMON_LIBRARIES);

            $start_of_week_identifier = 0;
        }
        // Defining the months of the year to allow translation of the months
        $MonthsLong = array(
            $translator->trans("JanuaryLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("FebruaryLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("MarchLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("AprilLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("MayLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("JuneLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("JulyLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("AugustLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("SeptemberLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("OctoberLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("NovemberLong", array(), Utilities::COMMON_LIBRARIES),
            $translator->trans("DecemberLong", array(), Utilities::COMMON_LIBRARIES)
        );

        $html[] = $this->render_header();

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Format', true) . 'TblChange.js'
        );

        $html[] = '<script>';
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
        $html[] = '<script>';
        $html[] = 'initCalendar(' . $start_of_week_identifier . ');';
        $html[] = '</script>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}