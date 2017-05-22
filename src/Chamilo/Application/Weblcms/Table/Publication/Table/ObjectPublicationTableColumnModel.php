<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableMultiColumnSortSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * The TableColumnModel for the object publication table
 * 
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */
class ObjectPublicationTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 7;
    const COLUMN_STATUS = 'status';
    const COLUMN_PUBLISHED_FOR = 'published_for';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Initializes the columns for the table
     * 
     * @param bool $addActionsColumn
     */
    public function initialize_columns($addActionsColumn = true)
    {
        $this->add_column(new StaticTableColumn(self::COLUMN_STATUS, '', null, 'publication_table_status_column'));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_PUBLICATION_DATE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_MODIFICATION_DATE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_PUBLISHER_ID));
        
        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_PUBLISHED_FOR, 
                Translation::getInstance()->getTranslation('PublishedFor', null, Manager::context())));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX, 
                null, 
                true, 
                null, 
                'publication_table_order_column'));
        
        if ($addActionsColumn)
        {
            $this->addActionsColumn();
        }
    }

    /**
     * Adds the actions column
     */
    protected function addActionsColumn()
    {
        $this->add_column(new ActionsTableColumn('publication_table_actions_column'));
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the display order column property
     * 
     * @return string
     */
    public function get_display_order_column_property()
    {
        return ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX;
    }

    /**
     * Checks whether or not a given column is a display order / sort column
     * 
     * @return bool
     */
    public function is_display_order_column()
    {
        $orderedColumns = $this->getCurrentOrderedColumns();
        $firstOrderedColumnData = array_shift($orderedColumns);

        $firstOrderedColumn = $firstOrderedColumnData[self::ORDER_COLUMN];
        $firstOrderedColumnOrder = $firstOrderedColumnData[self::ORDER_DIRECTION];

        $display_order_column_property = $this->get_display_order_column_property();

        if ($firstOrderedColumn && $display_order_column_property && $firstOrderedColumnOrder == SORT_ASC)
        {
            $current_column_property = $firstOrderedColumn->get_name();
            
            if ($current_column_property == $display_order_column_property)
            {
                return true;
            }
        }
        
        return false;
    }
}
