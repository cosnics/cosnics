<?php
namespace Chamilo\Core\Lynx\Source\Table\Source;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

class SourceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param $course Course
     * @return String
     */
    public function get_actions($registration)
    {
        $toolbar = new Toolbar();

        return $toolbar->as_html();
    }
}
