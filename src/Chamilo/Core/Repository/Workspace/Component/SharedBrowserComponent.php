<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Table\Workspace\Shared\SharedWorkspaceTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedBrowserComponent extends TabComponent implements TableSupport
{

    public function build()
    {
        $table = new SharedWorkspaceTable($this);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
    }
}