<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * The TableColumnModel for the object publication table
 * 
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */
class ObjectPublicationTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 6;
    const COLUMN_STATUS = 'status';
    const COLUMN_PUBLISHED_FOR = 'published_for';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self :: COLUMN_STATUS));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_PUBLICATION_DATE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_PUBLISHER_ID));
        
        $this->add_column(new StaticTableColumn(self :: COLUMN_PUBLISHED_FOR));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPublication :: class_name(), 
                ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX));

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
        return ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX;
    }

    /**
     * Checks whether or not a given column is a display order / sort column
     * 
     * @return bool
     */
    public function is_display_order_column()
    {
        $display_order_column_property = $this->get_display_order_column_property();
        $current_column = $this->get_column($this->get_default_order_column());
        
        if ($current_column && $display_order_column_property)
        {
            $current_column_property = $current_column->get_name();
            
            if ($current_column_property == $display_order_column_property)
            {
                return true;
            }
        }
        
        return false;
    }
}
