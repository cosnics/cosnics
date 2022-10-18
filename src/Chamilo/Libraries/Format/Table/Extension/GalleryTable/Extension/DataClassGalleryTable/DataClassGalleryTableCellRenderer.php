<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer;

/**
 * This class represents a cell renderer for a DataClass gallery table
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassGalleryTableCellRenderer extends GalleryTableCellRenderer
{

    /**
     * Define the unique identifier for the DataClass needed for e.g.
     * checkboxes
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    public function renderIdentifierCell($dataClass): string
    {
        return $dataClass->get_id();
    }
}
