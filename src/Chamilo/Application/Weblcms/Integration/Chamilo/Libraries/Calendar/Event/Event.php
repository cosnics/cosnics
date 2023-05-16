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
    private $courseId;

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
    public function __construct(
        $id = null, $startDate = null, $endDate = null, RecurrenceRules $recurrenceRules = null, $url = null,
        $title = null, $content = null,
        $source = null, $context = null, $courseId = null
    )
    {
        parent::__construct($id, $startDate, $endDate, $recurrenceRules, $url, $title, $content, $source, $context);
        $this->courseId = $courseId;
    }

    /**
     *
     * @return int
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     *
     * @param int $courseId
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }
}
