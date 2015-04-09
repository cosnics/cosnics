<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Table\ContentObjectRelMetadataElement;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table column model for the ContentObjectRelMetadataElement data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectRelMetadataElementTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE, 
                null, 
                false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Core\Metadata\Element\Storage\DataClass\Element :: class_name(), 
                \Chamilo\Core\Metadata\Element\Storage\DataClass\Element :: PROPERTY_NAME, 
                Translation :: get('MetadataElement'), 
                false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_REQUIRED));
    }
}