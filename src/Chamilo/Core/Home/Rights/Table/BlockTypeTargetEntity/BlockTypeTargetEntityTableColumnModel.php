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
    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(
                BlockTypeTargetEntity::class,
                BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE));
        
        $this->addColumn(
            new StaticTableColumn(
                'target_entities', 
                Translation::getInstance()->getTranslation('TargetEntities', null, Manager::context())));
    }
}