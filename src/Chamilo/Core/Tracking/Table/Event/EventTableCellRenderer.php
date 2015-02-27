<?php
namespace Chamilo\Core\Tracking\Table\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class EventTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $event)
    {
        switch ($column->get_name())
        {
            case Event :: PROPERTY_NAME :
                if ($event->get_active() == 1)
                {
                    return '<a href="' . $this->get_component()->get_event_viewer_url($event) . '">' . $event->get_name() .
                         '</a>';
                }
                else
                {
                    $event->get_name();
                }
        }
        return parent :: render_cell($column, $event);
    }

    public function get_actions($event)
    {
        $toolbar = new Toolbar();
        
        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    ($event->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'), 
                    ($event->get_active() == 1) ? Theme :: getInstance()->getCommonImagesPath() . 'action_visible.png' : Theme :: getInstance()->getCommonImagesPath() .
                         'action_invisible.png', 
                        $this->get_component()->get_change_active_url('event', $event->get_id()), 
                        ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Empty_event'), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_recycle_bin.png', 
                    $this->get_component()->get_empty_tracker_url('event', $event->get_id()), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
