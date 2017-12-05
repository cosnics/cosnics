<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DateSelectionRenderer
{

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

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
            new Button(
                $this->getTranslator()->trans('JumpTo', [], 'Chamilo\Libraries\Calendar'),
                null,
                null,
                Button::DISPLAY_LABEL,
                false,
                'btn-link'));
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
        $translator = $this->getTranslator();

        $monthNames = array(
            $translator->trans("JanuaryLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("FebruaryLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("MarchLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("AprilLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("MayLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("JuneLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("JulyLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("AugustLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("SeptemberLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("OctoberLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("NovemberLong", [], Utilities::COMMON_LIBRARIES),
            $translator->trans("DecemberLong", [], Utilities::COMMON_LIBRARIES));
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
