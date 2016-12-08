<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a cell renderer for a record gallery table Refactoring from ObjectTable to split between a
 * table based on a record and based on an object
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class RecordGalleryTableCellRenderer extends GalleryTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     * 
     * @param string[] $row
     *
     * @return int
     */
    public function render_id_cell($row)
    {
        return $row[DataClass::PROPERTY_ID];
    }
}
