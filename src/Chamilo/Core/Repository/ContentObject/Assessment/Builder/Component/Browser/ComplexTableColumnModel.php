<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\Browser;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.assessment.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class ComplexTableColumnModel extends \Chamilo\Core\Repository\Table\Complex\ComplexTableColumnModel
{
    const WEIGHT = 'weight';

    public function initialize_columns()
    {
        $this->addBasicColumns();
        $this->add_column(new StaticTableColumn(Translation :: get(self :: WEIGHT)));
    }
}
