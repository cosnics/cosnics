<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer;

/**
 * This class represents a cell renderer for a DataClass gallery table
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassGalleryTableCellRenderer extends GalleryTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */

    /**
     * Define the unique identifier for the DataClass needed for e.g.
     * checkboxes
     *
     * @param DataClass $data_class
     *
     * @return int
     */
    public function render_id_cell($data_class)
    {
        return $data_class->get_id();
    }
}
