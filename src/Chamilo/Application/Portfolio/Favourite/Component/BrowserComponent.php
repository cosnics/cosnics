<?php
namespace Chamilo\Application\Portfolio\Favourite\Component;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Table\Favourite\FavouriteTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Browser for the favourites of the current user
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     * Executes this component
     */
    function run()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the favourite users table
     */
    protected function renderTable()
    {
        $table = new FavouriteTable($this);
        
        return $table->as_html();
    }

    /**
     * Returns the condition
     * 
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
    }
}