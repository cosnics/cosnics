<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Table\Browser;

use Chamilo\Core\Repository\Table\Complex\ComplexTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: survey_page_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.survey_page.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class ComplexTableColumnModel extends ComplexTableColumnModel
{
    const WEIGHT = 'weight';

    public function initialize_columns()
    {
        parent :: initialize_columns();
        $this->add_column(new StaticTableColumn(Translation :: get(self :: WEIGHT)));
    }
}
