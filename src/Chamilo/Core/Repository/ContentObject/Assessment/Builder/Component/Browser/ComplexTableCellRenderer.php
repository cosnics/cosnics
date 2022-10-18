<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\Browser;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_builder.assessment.component.browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class ComplexTableCellRenderer extends \Chamilo\Core\Repository\Table\Complex\ComplexTableCellRenderer
{

    // Inherited
    public function renderCell(TableColumn $column, $cloi): string
    {
        switch ($column->get_name())
        {
            case Translation::get(ComplexTableColumnModel::WEIGHT) :
                return $cloi->get_weight();
        }

        return parent::renderCell($column, $cloi);
    }
}
