<?php
namespace Chamilo\Libraries\Calendar\Renderer\Form;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     * @var string
     */
    private $navigationUrl;

    /**
     *
     * @var int
     */
    private $currentTime;

    /**
     *
     * @param string $navigationUrl
     * @param integer $currentTime
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
    public function getNavigationUrl()
    {
        return $this->navigationUrl;
    }

    /**
     *
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->currentTime;
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
            new Button(Translation::get('JumpTo'), null, null, Button::DISPLAY_LABEL, false, 'btn-link'));
        $buttonToolbar->addItem($buttonGroup);
        
        $dateButton = new DropdownButton(date('j', $this->getCurrentTime()));
        
        foreach ($this->getDays() as $day)
        {
            $dayUrl = str_replace(
                Calendar::TIME_PLACEHOLDER, 
                mktime(null, null, null, date('n', $this->getCurrentTime()), $day, date('Y', $this->getCurrentTime())), 
                $this->getNavigationUrl());
            
            $classes = date('j', $this->getCurrentTime()) == $day ? 'selected' : 'not-selected';
            $dateButton->addSubButton(new SubButton($day, null, $dayUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }
        
        $months = $this->getMonths();
        $monthButton = new DropdownButton($months[date('n', $this->getCurrentTime())]);
        
        foreach ($this->getMonths() as $month => $monthLabel)
        {
            $monthUrl = str_replace(
                Calendar::TIME_PLACEHOLDER, 
                mktime(null, null, null, $month, date('j', $this->getCurrentTime()), date('Y', $this->getCurrentTime())), 
                $this->getNavigationUrl());
            
            $classes = date('n', $this->getCurrentTime()) == $month ? 'selected' : 'not-selected';
            $monthButton->addSubButton(
                new SubButton($monthLabel, null, $monthUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }
        
        $yearButton = new DropdownButton(date('Y', $this->getCurrentTime()));
        
        foreach ($this->getYears() as $year)
        {
            $yearUrl = str_replace(
                Calendar::TIME_PLACEHOLDER, 
                mktime(null, null, null, date('n', $this->getCurrentTime()), date('j', $this->getCurrentTime()), $year), 
                $this->getNavigationUrl());
            
            $classes = date('Y', $this->getCurrentTime()) == $year ? 'selected' : 'not-selected';
            $yearButton->addSubButton(new SubButton($year, null, $yearUrl, SubButton::DISPLAY_LABEL, false, $classes));
        }
        
        $buttonGroup->addButton($dateButton);
        $buttonGroup->addButton($monthButton);
        $buttonGroup->addButton($yearButton);
        
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        return $buttonToolbarRenderer;
    }

    /**
     *
     * @return int[]
     */
    public function getDays()
    {
        $numberDays = date('t', $this->getCurrentTime());
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
     * @return int[]
     */
    public function getYears()
    {
        $year = date('Y', $this->getCurrentTime());
        $years = array();
        
        for ($i = $year - 5; $i <= $year + 5; $i ++)
        {
            $years[$i] = $i;
        }
        
        return $years;
    }
}
