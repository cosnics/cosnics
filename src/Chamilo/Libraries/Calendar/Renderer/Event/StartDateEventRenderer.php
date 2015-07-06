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
abstract class StartDateEventRenderer extends EventRenderer
{

    /**
     *
     * @var int
     */
    private $start_date;

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     */
    public function __construct(Renderer $renderer, Event $event, $start_date)
    {
        $this->start_date = $start_date;

        parent :: __construct($renderer, $event);
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
     * @param int $start_date
     */
    public function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $start_date
     * @return StartDateEventRenderer
     */
    static public function factory(Renderer $renderer, Event $event, $start_date)
    {
        $event_renderer_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent($event :: context()) .
             '\Renderer\Event\Event' . $renderer :: class_name(false);
        return new $event_renderer_class_name($renderer, $event, $start_date);
    }
}
