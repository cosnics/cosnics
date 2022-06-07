<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;

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

        $html = [];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $DaysShort = array(
            $translator->trans("MondayShort", [], StringUtilities::LIBRARIES),
            $translator->trans("TuesdayShort", [], StringUtilities::LIBRARIES),
            $translator->trans("WednesdayShort", [], StringUtilities::LIBRARIES),
            $translator->trans("ThursdayShort", [], StringUtilities::LIBRARIES),
            $translator->trans("FridayShort", [], StringUtilities::LIBRARIES),
            $translator->trans("SaturdayShort", [], StringUtilities::LIBRARIES)
        );

        // Defining the days of the week to allow translation of the days
        $DaysLong = array(
            $translator->trans("MondayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("TuesdayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("WednesdayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("ThursdayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("FridayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("SaturdayLong", [], StringUtilities::LIBRARIES)
        );

        $start_of_week =
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));
        
        if ($start_of_week == 'sunday')
        {
            array_unshift($DaysShort, $translator->trans("SundayShort", [], StringUtilities::LIBRARIES));
            array_unshift($DaysLong, $translator->trans("SundayLong", [], StringUtilities::LIBRARIES));

            $start_of_week_identifier = 1;
        }
        else
        {
            $DaysShort[] = $translator->trans("SundayShort", [], StringUtilities::LIBRARIES);
            $DaysLong[] = $translator->trans("SundayLong", [], StringUtilities::LIBRARIES);

            $start_of_week_identifier = 0;
        }
        // Defining the months of the year to allow translation of the months
        $MonthsLong = array(
            $translator->trans("JanuaryLong", [], StringUtilities::LIBRARIES),
            $translator->trans("FebruaryLong", [], StringUtilities::LIBRARIES),
            $translator->trans("MarchLong", [], StringUtilities::LIBRARIES),
            $translator->trans("AprilLong", [], StringUtilities::LIBRARIES),
            $translator->trans("MayLong", [], StringUtilities::LIBRARIES),
            $translator->trans("JuneLong", [], StringUtilities::LIBRARIES),
            $translator->trans("JulyLong", [], StringUtilities::LIBRARIES),
            $translator->trans("AugustLong", [], StringUtilities::LIBRARIES),
            $translator->trans("SeptemberLong", [], StringUtilities::LIBRARIES),
            $translator->trans("OctoberLong", [], StringUtilities::LIBRARIES),
            $translator->trans("NovemberLong", [], StringUtilities::LIBRARIES),
            $translator->trans("DecemberLong", [], StringUtilities::LIBRARIES)
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