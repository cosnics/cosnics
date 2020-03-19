<?php
namespace Chamilo\Core\Repository\Table\Export;

use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Translation\Translation;
use HTML_Table;

class ExportTable extends SortableTableFromArray
{

    public function toHtml($totalValue = null, $totalColumn = null)
    {
        $this->initializeTable();
        
        $tableData = $this->getData();
        
        foreach ($tableData as $index => & $row)
        {
            $rowId = $row[0];
            $row = $this->filterData($row);
            $currentRow = $this->addRow($row);
            $this->setRowAttributes($currentRow, array('id' => 'row_' . $rowId), true);
        }
        
        $this->altRowAttributes(0, array('class' => 'row_even'), array('class' => 'row_odd'), true);
        
        $headerAttributes = $this->getHeaderAttributes();
        foreach ($headerAttributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }
        
        $contentCellAttributes = $this->getContentCellAttributes();
        foreach ($contentCellAttributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }
        
        if ($totalValue && $totalColumn)
        {
            $dataRow = array();
            $dataRow[$totalColumn] = $totalValue;
            $dataRow[0] = Translation::get('Total');
            
            $this->addRow($dataRow);
            
            $this->setCellAttributes(
                ($this->countData()), 
                0, 
                'colspan="' . ($totalColumn) . '" style="font-weight:bold; text-transform:uppercase; text-align:right;"');
            $this->setCellAttributes(
                ($this->countData()), 
                $totalColumn, 
                'colspan="' . ($this->getColCount() - $totalColumn) .
                     '" style="font-weight:bold; text-transform:uppercase;"');
        }
        
        $html[] = '<div class="table-responsive">';
        $html[] = HTML_Table::toHtml();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
