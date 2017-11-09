<?php
namespace Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntity;

use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Builds the table for the BlockTypeTargetEntity data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntityTableColumnModel extends RecordTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                BlockTypeTargetEntity::class_name(), 
                BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE));
        
        $this->add_column(
            new StaticTableColumn(
                'target_entities', 
                Translation::getInstance()->getTranslation('TargetEntities', null, Manager::context())));
    }
}