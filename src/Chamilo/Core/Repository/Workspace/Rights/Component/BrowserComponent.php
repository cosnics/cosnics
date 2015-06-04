<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation\EntityRelationTable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends TabComponent implements TableSupport
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBarRenderer
     */
    private $actionBar;

    public function build()
    {
        $table = new EntityRelationTable($this);

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