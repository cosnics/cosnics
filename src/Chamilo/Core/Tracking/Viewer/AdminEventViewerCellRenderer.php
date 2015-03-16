<?php
namespace Chamilo\Core\Tracking\Viewer;

use Chamilo\Core\Tracking\Storage\DataClass\TrackerRegistration;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: admin_event_viewer_cell_renderer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component.admin_event_viewer
 */

/**
 * Class used to retrieve the modification links for the admin events viewer tables
 */
class AdminEventViewerCellRenderer
{

    /**
     * Eventviewer where this cellrenderer belongs to
     */
    private $eventviewer;

    private $event;

    /**
     * Constructor
     *
     * @param AdminTrackingBrowser $browser The browser where this renderer belongs to
     */
    public function __construct($eventviewer, $event)
    {
        $this->eventviewer = $eventviewer;
        $this->event = $event;
    }

    /**
     * Creates the modification links for the given tracker
     *
     * @param Tracker $tracker the tracker
     * @return string The modification links for the given tracker
     */
    public function get_modification_links($tracker)
    {
        $toolbar = new Toolbar();

        if ($this->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    ($tracker->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'),
                    ($tracker->get_active() == 1) ? Theme :: getInstance()->getCommonImagePath('Action/Visible') : Theme :: getInstance()->getCommonImagePath(
                        'action_invisible'),
                    $this->eventviewer->get_change_active_url('tracker', $this->event->get_id(), $tracker->get_id()),
                    ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Empty_Tracker'),
                    Theme :: getInstance()->getCommonImagePath('Action/RecycleBin'),
                    $this->eventviewer->get_empty_tracker_url('tracker', $this->event->get_id()),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }
        return $toolbar->as_html();
    }

    /**
     * Renders a cell
     *
     * @param string $property the property name
     * @param Tracker $tracker the tracker
     */
    public function render_cell($property, $tracker)
    {
        switch ($property)
        {
        }

        return $tracker->get_default_property($property);
    }

    /**
     * Returns the properties that will become the columns
     *
     * @return array of properties
     */
    public function get_properties()
    {
        return array(
            TrackerRegistration :: PROPERTY_TRACKER => 'Tracker',
            TrackerRegistration :: PROPERTY_CONTEXT => 'Application');
    }
}
