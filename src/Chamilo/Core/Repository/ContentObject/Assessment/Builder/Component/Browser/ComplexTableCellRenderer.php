<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\Browser;

use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.assessment.component.browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class ComplexTableCellRenderer extends \Chamilo\Core\Repository\Table\Complex\ComplexTableCellRenderer
{

    // Inherited
    public function render_cell($column, $cloi)
    {
        switch ($column->get_name())
        {
            case Translation :: get(ComplexTableColumnModel :: WEIGHT) :
                return $cloi->get_weight();
        }

        return parent :: render_cell($column, $cloi);
    }
}
