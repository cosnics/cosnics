<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Workspace\Table\Share\ShareTableCellRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

class SharedInTableCellRenderer extends ShareTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($workspace)
    {
        $toolbar = new Toolbar();

        return $toolbar->as_html();
    }
}
