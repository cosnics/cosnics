<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Table\PublicationTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which displays user's publications.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class BrowserComponent extends Manager implements TableSupport, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $output = $this->get_publications_html();

        $html = [];

        $html[] = $this->render_header();
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the table which shows the users's publication
     */
    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);

        $table = new PublicationTable($this);
        return $table->as_html();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return null;
    }
}
