<?php
namespace Chamilo\Libraries\Calendar\Event;

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
     * @var \Chamilo\Libraries\Calendar\Event\RecurrenceRules
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
     * @param string $url
     * @param string $title
     * @param string $content
     * @param string $source
     * @param string $context
     */
    public function __construct($id, $startDate, $endDate, RecurrenceRules $recurrenceRules = null, $url, $title, $content,
        $source, $context)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->recurrenceRules = $recurrenceRules;
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
        $this->source = $source;
        $this->context = $context;
    }

    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return int
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\RecurrenceRules
     */
    public function getRecurrenceRules()
    {
        return $this->recurrenceRules;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules $recurrenceRules
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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
     * @param int $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @param int $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
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
    public static function package()
    {
        return static :: context();
    }
}
