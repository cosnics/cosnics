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
abstract class HourStepEventRenderer extends StartDateEventRenderer
{

    /**
     *
     * @var int
     */
    private $hour_step;

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     * @param int $hour_step
     */
    public function __construct(Renderer $renderer, Event $event, $start_date, $hour_step)
    {
        $this->hour_step = $hour_step;

        parent :: __construct($renderer, $event, $start_date);
    }

    /**
     *
     * @return int
     */
    public function get_hour_step()
    {
        return $this->hour_step;
    }

    /**
     *
     * @param int $hour_step
     */
    public function set_hour_step($hour_step)
    {
        $this->hour_step = $hour_step;
    }

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     * @return HourStepEventRenderer
     */
    static public function factory(Renderer $renderer, Event $event, $start_date, $hour_step)
    {
        $event_renderer_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent($event :: context()) .
             '\Renderer\Event\Type\Event' . $renderer :: class_name(false);
        return new $event_renderer_class_name($renderer, $event, $start_date, $hour_step);
    }
}
