<?php
namespace Chamilo\Application\Survey\Export\Table\TrackerTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExportTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($object)
    {
        $toolbar = new Toolbar();

        if ($object->get_export_job_id() != 0 && $object->get_status() == Export :: STATUS_EXPORT_IN_QUEUE)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->browser->get_export_tracker_delete_url($object),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        return $toolbar->as_html();
    }
}
?>