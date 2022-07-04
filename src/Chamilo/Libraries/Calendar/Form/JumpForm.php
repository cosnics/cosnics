<?php
namespace Chamilo\Libraries\Calendar\Form;

use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class JumpForm
{

    /**
     *
     * @var int
     */
    private $currentTime;

    /**
     *
     * @var string
     */
    private $navigationUrl;

    /**
     *
     * @param string $navigationUrl
     * @param int $currentTime
     */
    public function __construct($navigationUrl, $currentTime = null)
    {
        $this->navigationUrl = $navigationUrl;
        $this->currentTime = is_null($currentTime) ? intval($currentTime) : $currentTime;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return $this->getButtonToolBarRenderer()->render();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    private function getButtonToolBarRenderer()
    {
        $buttonToolbar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolbar->addItem(
            new Button(Translation::get('JumpTo'), null, null, Button::DISPLAY_LABEL, null, ['btn-link'])
        );
        $buttonToolbar->addItem($buttonGroup);

        $dateButton = new DropdownButton(date('j', $this->getCurrentTime()));

        foreach ($this->getDays() as $day)
        {
            $dayUrl = str_replace(
                CalendarTable::TIME_PLACEHOLDER,
                mktime(null, null, null, date('n', $this->getCurrentTime()), $day, date('Y', $this->getCurrentTime())),
                $this->getNavigationUrl()
            );

            $isActive = date('j', $this->getCurrentTime()) == $day;
            $dateButton->addSubButton(
                new SubButton($day, null, $dayUrl, SubButton::DISPLAY_LABEL, null, [], null, $isActive)
            );
        }

        $months = $this->getMonths();
        $monthButton = new DropdownButton($months[date('n', $this->getCurrentTime())]);

        foreach ($this->getMonths() as $month => $monthLabel)
        {
            $monthUrl = str_replace(
                CalendarTable::TIME_PLACEHOLDER, mktime(
                null, null, null, $month, date('j', $this->getCurrentTime()), date('Y', $this->getCurrentTime())
            ), $this->getNavigationUrl()
            );

            $isActive = date('n', $this->getCurrentTime()) == $month;
            $monthButton->addSubButton(
                new SubButton($monthLabel, null, $monthUrl, SubButton::DISPLAY_LABEL, null, [], null, $isActive)
            );
        }

        $yearButton = new DropdownButton(date('Y', $this->getCurrentTime()));

        foreach ($this->getYears() as $year)
        {
            $yearUrl = str_replace(
                CalendarTable::TIME_PLACEHOLDER,
                mktime(null, null, null, date('n', $this->getCurrentTime()), date('j', $this->getCurrentTime()), $year),
                $this->getNavigationUrl()
            );

            $isActive = date('Y', $this->getCurrentTime()) == $year;
            $yearButton->addSubButton(
                new SubButton($year, null, $yearUrl, SubButton::DISPLAY_LABEL, null, [], null, $isActive)
            );
        }

        $buttonGroup->addButton($dateButton);
        $buttonGroup->addButton($monthButton);
        $buttonGroup->addButton($yearButton);

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     *
     * @return int
     */
    public function getCurrentTime()
    {
        return $this->currentTime;
    }

    /**
     *
     * @return int
     */
    public function getDays()
    {
        $numberDays = date('t', $this->getCurrentTime());
        $days = [];

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
        $translator = Translation::getInstance();

        $monthNames = array(
            $translator->getTranslation('JanuaryLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('FebruaryLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('MarchLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('AprilLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('MayLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('JuneLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('JulyLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('AugustLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('SeptemberLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('OctoberLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('NovemberLong', [], StringUtilities::LIBRARIES),
            $translator->getTranslation('DecemberLong', [], StringUtilities::LIBRARIES)
        );
        $months = [];

        foreach ($monthNames as $key => $month)
        {
            $months[$key + 1] = $month;
        }

        return $months;
    }

    /**
     *
     * @return string
     */
    public function getNavigationUrl()
    {
        return $this->navigationUrl;
    }

    /**
     *
     * @return int
     */
    public function getYears()
    {
        $year = date('Y', $this->getCurrentTime());
        $years = [];

        for ($i = $year - 5; $i <= $year + 5; $i ++)
        {
            $years[$i] = $i;
        }

        return $years;
    }
}
