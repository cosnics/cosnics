<?php
namespace Chamilo\Core\Repository\Table\Export;

use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Platform\Translation;
use HTML_Table;

class ExportTable extends SortableTableFromArray
{

    /**
     * Get table data to show on current page
     * 
     * @see SortableTable#get_table_data
     */
    public function get_table_data()
    {
        return $this->get_data();
    }

    public function as_html($total_value, $total_column)
    {
        return $this->get_table_html($total_value, $total_column);
    }

    /**
     * Get the HTML-code with the data-table.
     */
    public function get_table_html($total_value, $total_column)
    {
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
        
        if ($total_value && $total_column)
        {
            $data_row = array();
            $data_row[$total_column] = $total_value;
            $data_row[0] = Translation :: get('Total');
            $this->addRow($data_row);
            $this->setCellAttributes(
                ($this->get_total_number_of_items()), 
                0, 
                'colspan="' . ($total_column) . '" style="font-weight:bold; text-transform:uppercase; text-align:right;"');
            $this->setCellAttributes(
                ($this->get_total_number_of_items()), 
                $total_column, 
                'colspan="' . ($this->getColCount() - $total_column) .
                     '" style="font-weight:bold; text-transform:uppercase;"');
        }
        return HTML_Table :: toHTML();
    }
}
