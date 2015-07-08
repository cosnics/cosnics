<?php
namespace Chamilo\Libraries\Calendar\Event;

/**
 * An event in the personal calendar as a shell around concepts which exist in the integrating contexts
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Event
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var int
     */
    private $start_date;

    /**
     *
     * @var int
     */
    private $end_date;

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
     * @var int
     */
    private $id;

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
        $this->start_date = $startDate;
        $this->end_date = $endDate;
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
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @return int
     */
    public function get_start_date()
    {
        return $this->start_date;
    }

    /**
     *
     * @return int
     */
    public function get_end_date()
    {
        return $this->end_date;
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
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @return string
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     *
     * @return string
     */
    public function get_source()
    {
        return $this->source;
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param int $start_date
     */
    public function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     *
     * @param int $end_date
     */
    public function set_end_date($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param string $content
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @param string $source
     */
    public function set_source($source)
    {
        $this->source = $source;
    }

    /**
     *
     * @param string $context
     */
    public function set_context($context)
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
