<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Table\ContentObjectPropertyRelMetadataElement;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table column model for the ContentObjectPropertyRelMetadataElement data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPropertyRelMetadataElementTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPropertyRelMetadataElement :: class_name(), 
                ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE, 
                null, 
                false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPropertyRelMetadataElement :: class_name(), 
                ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME, 
                null, 
                false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element :: class_name(), 
                \Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element :: PROPERTY_NAME, 
                Translation :: get('MetadataElement'), 
                false));
    }
}