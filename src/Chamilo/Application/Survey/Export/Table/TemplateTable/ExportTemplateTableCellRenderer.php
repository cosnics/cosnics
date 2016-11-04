<?php
namespace Chamilo\Application\Survey\Export\Table\TemplateTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\ExportTemplate;
use Chamilo\Application\Survey\Export\Storage\DataClass\SynchronizeAnswer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExportTemplateTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    function render_cell($column, $export_template)
    {
        switch ($column->get_name())
        {
            case ExportTemplate :: PROPERTY_NAME :
                
                $title = parent :: render_cell($column, $export_template);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                
                $tracker = $this->component->get_tracker();
                if ($tracker)
                {
                    if ($tracker->get_status() == SynchronizeAnswer :: STATUS_SYNCHRONIZED)
                    {
                        return '<a href="' . htmlentities($this->component->get_export_url($export_template)) .
                             '" title="' . $title . '">' . $title_short . '</a>';
                    }
                    else
                    {
                        return $title_short;
                    }
                }
                else
                {
                    return $title_short;
                }
            
            case ExportTemplate :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $export_template));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return StringUtilities :: getInstance()->truncate($description);
        }
        
        return parent :: render_cell($column, $export_template);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        
        $tracker = $this->component->get_tracker();
        if ($tracker)
        {
            if ($tracker->get_status() == SynchronizeAnswer :: STATUS_SYNCHRONIZED)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('CreateExport'), 
                        Theme :: getInstance()->getCommonImagePath('Action/Export'), 
                        $this->component->get_export_url($object), 
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('CreateExport'), 
                        Theme :: getInstance()->getCommonImagePath('Action/ExportNa'), 
                        null, 
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('CreateExport'), 
                    Theme :: getInstance()->getCommonImagePath('Action/ExportNa'), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath('Action/Delete'), 
                $this->component->get_export_template_delete_url($object), 
                ToolbarItem :: DISPLAY_ICON, 
                true));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath('Action/Edit'), 
                $this->component->get_export_template_edit_url($object), 
                ToolbarItem :: DISPLAY_ICON));
        
        if ($this->component->get_user()->is_platform_admin() ||
             $object->get_owner_id() == $this->component->get_user_id())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'), 
                    $this->component->get_export_template_rights_editor_url($object), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
?>