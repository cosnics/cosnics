<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarPopupComponent extends Manager implements NoAuthenticationSupportInterface
{
    public function run(?User $currentUser = null): Response
    {
        $translator = $this->getTranslator();

        $html = [];

        $DaysShort = [
            $translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
            $translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
            $translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
            $translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
            $translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
            $translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES)
        ];

        $startOfWeek = $this->getUserService()->findUserSetting(
            $currentUser, 'cosnics.libraries.calendar.firstDayOfWeek',
            $this->getContainer()->getParameter('cosnics.libraries.calendar.firstDayOfWeek')
        );

        if ($startOfWeek == 'sunday') {
            array_unshift($DaysShort, $translator->trans('SundayShort', [], StringUtilities::LIBRARIES));

            $startOfWeekIdentifier = 1;
        }
        else {
            $DaysShort[] = $translator->trans('SundayShort', [], StringUtilities::LIBRARIES);
            $startOfWeekIdentifier = 0;
        }
        // Defining the months of the year to allow translation of the months
        $MonthsLong = [
            $translator->trans('JanuaryLong', [], StringUtilities::LIBRARIES),
            $translator->trans('FebruaryLong', [], StringUtilities::LIBRARIES),
            $translator->trans('MarchLong', [], StringUtilities::LIBRARIES),
            $translator->trans('AprilLong', [], StringUtilities::LIBRARIES),
            $translator->trans('MayLong', [], StringUtilities::LIBRARIES),
            $translator->trans('JuneLong', [], StringUtilities::LIBRARIES),
            $translator->trans('JulyLong', [], StringUtilities::LIBRARIES),
            $translator->trans('AugustLong', [], StringUtilities::LIBRARIES),
            $translator->trans('SeptemberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('OctoberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('NovemberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('DecemberLong', [], StringUtilities::LIBRARIES)
        ];

        $html[] = $this->getHeaderRenderer()->render();

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Libraries\Format') . 'TblChange.js'
        );

        $html[] = '<script>';
        $html[] = '/* <![CDATA[ */';
        $html[] = 'var month_names = new Array(';

        foreach ($MonthsLong as $month) {
            $html[] = '"' . $month . '",';
        }
        $html[] = '"");';

        $html[] = 'var day_names = new Array(';

        foreach ($DaysShort as $day) {
            $html[] = '"' . $day . '",';
        }
        $html[] = '"");';
        $html[] = '/* ]]> */';
        $html[] = '</script>';

        $html[] = '<div id="calendar_data"></div>';
        $html[] = '<div id="clock_data"></div>';
        $html[] = '<script>';
        $html[] = 'initCalendar(' . $startOfWeekIdentifier . ');';
        $html[] = '</script>';
        $html[] = $this->getFooterRenderer()->render();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->getService(BaseFooterRenderer::class);
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->getService(BaseHeaderRenderer::class);
    }
}