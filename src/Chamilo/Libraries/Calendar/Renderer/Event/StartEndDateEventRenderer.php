<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event;



use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package libraries\calendar\renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class StartEndDateEventRenderer extends StartDateEventRenderer
{

    /**
     *
     * @var int
     */
    private $end_date;

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     * @param int $end_date
     */
    public function __construct(Renderer $renderer, Event $event, $start_date, $end_date)
    {
        $this->end_date = $end_date;

        parent :: __construct($renderer, $event, $start_date);
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
     * @param int $end_date
     */
    public function set_end_date($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     * @param int $end_date
     * @return StartEndDateEventRenderer
     */
    static public function factory(Renderer $renderer, Event $event, $start_date, $end_date)
    {
        $event_renderer_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent($event :: context()) . '\Renderer\Event\Event' .
             $renderer :: class_name(false);
        return new $event_renderer_class_name($renderer, $event, $start_date, $end_date);
    }
}
