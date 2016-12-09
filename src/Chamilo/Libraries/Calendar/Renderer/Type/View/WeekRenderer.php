<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Table\Type\WeekCalendar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WeekRenderer extends FullTableRenderer
{

    /**
     *
     * @var integer
     */
    private $hourStep;

    /**
     *
     * @var integer
     */
    private $startHour;

    /**
     *
     * @var integer
     */
    private $endHour;

    /**
     *
     * @var boolean
     */
    private $hideOtherHours;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $displayTime
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @param string $linkTarget
     * @param integer $hourStep
     * @param integer $startHour
     * @param integer $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime, 
        $viewActions = array(), $linkTarget = '', $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false)
    {
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;
        
        parent::__construct($dataProvider, $legend, $displayTime, $viewActions, $linkTarget);
    }

    /**
     *
     * @return int
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     *
     * @param int $hourStep
     */
    public function setHourStep($hourStep)
    {
        $this->hourStep = $hourStep;
    }

    /**
     *
     * @return int
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     *
     * @param int $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     *
     * @return int
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     *
     * @param int $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     *
     * @return int
     */
    public function getHideOtherHours()
    {
        return $this->hideOtherHours;
    }

    /**
     *
     * @param int $endHour
     */
    public function setHideOtherHours($hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\WeekCalendar
     */
    public function initializeCalendar()
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendar::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;
        $dayUrlTemplate = new Redirect($displayParameters);
        
        return new WeekCalendar(
            $this->getDisplayTime(), 
            $dayUrlTemplate->getUrl(), 
            $this->getHourStep(), 
            $this->getStartHour(), 
            $this->getEndHour(), 
            $this->getHideOtherHours(), 
            array('table-calendar-week'));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function renderFullCalendar()
    {
        $calendar = $this->getCalendar();
        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->getDisplayTime()))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));
        
        $events = $this->getEvents($fromDate, $toDate);
        
        $startTime = $calendar->getStartTime();
        $endTime = $toDate;
        
        $tableDate = $startTime;
        
        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendar->getHourStep() . ' Hours', $tableDate);
            
            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();
                
                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($calendar->getHourStep());
                    
                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);
                    
                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }
            $tableDate = $nextTableDate;
        }
        
        return $calendar->render();
    }

    /**
     *
     * @return string
     */
    public function renderTitle()
    {
        $weekNumber = date('W', $this->getDisplayTime());
        
        return Translation::get('Week', null, Utilities::COMMON_LIBRARIES) . ' ' . $weekNumber . ' : ' .
             DatetimeUtilities::format_locale_date('%A %d %B %Y', $this->getCalendar()->getStartTime()) . ' - ' . DatetimeUtilities::format_locale_date(
                '%A %d %B %Y', 
                strtotime('+6 Days', $this->getCalendar()->getStartTime()));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime()
    {
        return strtotime('-1 Week', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime()
    {
        return strtotime('+1 Week', $this->getDisplayTime());
    }
}
