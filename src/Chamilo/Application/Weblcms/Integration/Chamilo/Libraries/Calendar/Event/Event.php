<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Calendar\Event\RecurrenceRules;
/**
 *
 * @package application\weblcms\integration\libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event
{

    /**
     *
     * @var int
     */
    private $course_id;

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
     * @param integer $courseId
     */
    public function __construct($id, $startDate, $endDate, RecurrenceRules $recurrenceRules, $url, $title, $content, $source, $context, $courseId)
    {
        parent :: __construct($id, $startDate, $endDate, $recurrenceRules, $url, $title, $content, $source, $context);
        $this->course_id = $courseId;
    }

    /**
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->course_id;
    }

    /**
     *
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->course_id = $course_id;
    }
}
