<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules;

/**
 * An event in the personal calendar as a shell around concepts which exist in the integrating contexts
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var integer
     */
    private $id;

    /**
     *
     * @var integer
     */
    private $startDate;

    /**
     *
     * @var integer
     */
    private $endDate;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules
     */
    private $recurrenceRules;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var string
     */
    private $content;

    /**
     *
     * @var string
     */
    private $location;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\EventSource
     */
    private $source;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @param integer $id
     * @param integer $startDate
     * @param integer $endDate
     * @param RecurrenceRules $recurrenceRules
     * @param string $url
     * @param string $title
     * @param string $content
     * @param string $location
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     * @param string $context
     */
    public function __construct($id = null, $startDate = null, $endDate = null, RecurrenceRules $recurrenceRules = null, $url = null,
        $title = null, $content = null, $location = null, EventSource $source = null, $context = null)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->recurrenceRules = $recurrenceRules ?: new RecurrenceRules();
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
        $this->location = $location;
        $this->source = $source;
        $this->context = $context;
    }

    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param integer $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @param integer $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules
     */
    public function getRecurrenceRules()
    {
        return $this->recurrenceRules;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules $recurrenceRules
     */
    public function setRecurrenceRules($recurrenceRules)
    {
        $this->recurrenceRules = $recurrenceRules;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\EventSource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @return boolean
     */
    public function isAllDay()
    {
        $startDate = new \DateTime();
        $startDate->setTimestamp($this->getStartDate());

        $endDate = new \DateTime();
        $endDate->setTimestamp($this->getEndDate());

        if ($startDate->format('H') == 0 && $startDate->format('i') == 0 &&
             $this->getEndDate() >= $startDate->modify('+1 day')->getTimestamp())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }
}
