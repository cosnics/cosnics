<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use HTML_Table;

class SortableTable extends SortableTableFromArray
{

    public function toHtml()
    {
        $tableData = $this->getData($this->getFrom());

        foreach ($tableData as $index => & $row)
        {
            $rowId = $row[0];
            $row = $this->filterData($row);
            $currentRow = $this->addRow($row);
            $this->setRowAttributes($currentRow, array('id' => 'row_' . $rowId), true);
        }

        $this->altRowAttributes(0, array('class' => 'row_even'), array('class' => 'row_odd'), true);

        foreach ($this->getHeaderAttributes() as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }
        foreach ($this->getCellAttributes() as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }

        return HTML_Table :: toHtml();
    }
}
