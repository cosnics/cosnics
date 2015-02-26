<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Core\Tracking\Viewer\AdminEventViewerActionHandler;
use Chamilo\Core\Tracking\Viewer\AdminEventViewerCellRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\SimpleTable;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: admin_event_viewer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component
 */
/**
 * Component for viewing tracker events
 */
class AdminEventViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $event_id = Request :: get(self :: PARAM_EVENT_ID);

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $event = $this->retrieve_event($event_id);

        $cellrenderer = new AdminEventViewerCellRenderer($this, $event);
        $actionhandler = new AdminEventViewerActionHandler($this, $event);

        $trackers = $this->retrieve_trackers_from_event($event_id);
        $trackertable = new SimpleTable($trackers, $cellrenderer, $actionhandler, "trackertable");

        $html = array();

        $html[] = $this->render_header();
        $html[] = Translation :: get('You_are_viewing_trackers_for_event') . ': ' . $event->get_name() . '<br /><br />';
        $html[] = $trackertable->toHTML();
        $html[] = $this->render_footer();

        return implode("\n", $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browser_url(), Translation :: get('TrackingManagerAdminEventBrowserComponent')));
        $breadcrumbtrail->add_help('tracking_event_viewer');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_EVENT_ID);
    }
}
