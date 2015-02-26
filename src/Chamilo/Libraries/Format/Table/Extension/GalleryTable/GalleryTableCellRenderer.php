<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Format\Table\TableComponent;
/**
 * This class represents a cell renderer for a gallery table
 *
 * Refactoring from GalleryObjectTable to support the new Table structure
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTableCellRenderer extends TableCellRenderer
{
    /****************************************************************************************************************
     * Constructor                                                                                                  *
     ****************************************************************************************************************/

    /**
     * Constructor
     *
     * @param Table $table
     *
     * @throws \Exception
     */
    public function __construct($table)
    {
        TableComponent :: __construct($table);
    }

    /****************************************************************************************************************
     * Implemented Functionality                                                                                    *
     ****************************************************************************************************************/

    /**
     * Renders a single cell
     *
     * @param mixed $result
     *
     * @return String
     */
    public function render_cell($result)
    {
        $html = array();

        if($this instanceof TableCellRendererActionsColumnSupport)
        {
            $html[] = '<div style="width: 20px; float: right;">';
            $html[] = $this->get_actions($result);
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }
}
