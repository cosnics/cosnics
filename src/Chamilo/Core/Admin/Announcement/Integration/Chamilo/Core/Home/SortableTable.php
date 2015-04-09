<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use HTML_Table;

class SortableTable extends SortableTableFromArray
{

    /**
     * Get table data to show on current page
     * 
     * @see SortableTable#get_table_data
     */
    function get_table_data()
    {
        return $this->get_data();
    }

    function as_html()
    {
        return $this->get_table_html();
    }

    /**
     * Get the HTML-code with the data-table.
     */
    function get_table_html()
    {
        // Make sure the header isn't dragable or droppable
        // $this->setRowAttributes(0, array('class' => 'nodrag nodrop'), true);
        
        // Now process the rest of the table
        $pager = $this->get_pager();
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        $table_data = $this->get_table_data($from);
        
        foreach ($table_data as $index => & $row)
        {
            $row_id = $row[0];
            $row = $this->filter_data($row);
            $current_row = $this->addRow($row);
            $this->setRowAttributes($current_row, array('id' => 'row_' . $row_id), true);
        }
        
        $this->altRowAttributes(0, array('class' => 'row_even'), array('class' => 'row_odd'), true);
        
        foreach ($this->th_attributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }
        foreach ($this->td_attributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }
        
        return HTML_Table :: toHTML();
    }
}
