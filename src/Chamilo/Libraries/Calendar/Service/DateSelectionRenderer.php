<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DateSelectionRenderer
{

    /**
     *
     * @param string $navigationUrl
     * @param integer $currentRendererTime
     * @return string
     */
    public function render($navigationUrl, $currentRendererTime)
    {
        $buttonToolBar = $this->getButtonToolBar($navigationUrl, $currentRendererTime);
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @param string $navigationUrl
     * @param integer $currentRendererTime
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    protected function getButtonToolBar($navigationUrl, $currentRendererTime)
    {
        $buttonToolbar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolbar->addItem(
            new Button(Translation::get('JumpTo'), null, null, Button::DISPLAY_LABEL, false, 'btn-link'));
        $buttonToolbar->addItem($buttonGroup);

        $dateButton = new DropdownButton(date('j', $currentRendererTime));

        foreach ($this->getDays($currentRendererTime) as $day)
        {
            $dayUrl = str_replace(
                Calendar::TIME_PLACEHOLDER,
                mktime(null, null, null, date('n', $currentRendererTime), $day, date('Y', $currentRendererTime)),
                $navigationUrl);

            $classes = date('j', $currentRendererTime) == $day ? 'selected' : 'not-selected';
            $dateButton->addSubButton(new SubButton($day, null, $dayUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }

        $months = $this->getMonths();
        $monthButton = new DropdownButton($months[date('n', $currentRendererTime)]);

        foreach ($this->getMonths() as $month => $monthLabel)
        {
            $monthUrl = str_replace(
                Calendar::TIME_PLACEHOLDER,
                mktime(null, null, null, $month, date('j', $currentRendererTime), date('Y', $currentRendererTime)),
                $navigationUrl);

            $classes = date('n', $currentRendererTime) == $month ? 'selected' : 'not-selected';
            $monthButton->addSubButton(
                new SubButton($monthLabel, null, $monthUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }

        $yearButton = new DropdownButton(date('Y', $currentRendererTime));

        foreach ($this->getYears($currentRendererTime) as $year)
        {
            $yearUrl = str_replace(
                Calendar::TIME_PLACEHOLDER,
                mktime(null, null, null, date('n', $currentRendererTime), date('j', $currentRendererTime), $year),
                $navigationUrl);

            $classes = date('Y', $currentRendererTime) == $year ? 'selected' : 'not-selected';
            $yearButton->addSubButton(new SubButton($year, null, $yearUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }

        $buttonGroup->addButton($dateButton);
        $buttonGroup->addButton($monthButton);
        $buttonGroup->addButton($yearButton);

        return $buttonToolbar;
    }

    /**
     *
     * @return integer[]
     */
    public function getDays($currentRendererTime)
    {
        $numberDays = date('t', $currentRendererTime);
        $days = array();

        for ($i = 1; $i <= $numberDays; $i ++)
        {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     *
     * @return string[]
     */
    public function getMonths()
    {
        $monthNames = array(
            Translation::get("JanuaryLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("FebruaryLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("MarchLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("AprilLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("MayLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("JuneLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("JulyLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("AugustLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("SeptemberLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("OctoberLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("NovemberLong", null, Utilities::COMMON_LIBRARIES),
            Translation::get("DecemberLong", null, Utilities::COMMON_LIBRARIES));
        $months = array();

        foreach ($monthNames as $key => $month)
        {
            $months[$key + 1] = $month;
        }

        return $months;
    }

    /**
     *
     * @return integer[]
     */
    public function getYears($currentRendererTime)
    {
        $year = date('Y', $currentRendererTime);
        $years = array();

        for ($i = $year - 5; $i <= $year + 5; $i ++)
        {
            $years[$i] = $i;
        }

        return $years;
    }
}
