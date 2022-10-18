<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\Browser;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_builder.assessment.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class ComplexTableColumnModel extends \Chamilo\Core\Repository\Table\Complex\ComplexTableColumnModel
{
    const WEIGHT = 'weight';

    public function initializeColumns()
    {
        $this->addBasicColumns();
        $this->addColumn(new StaticTableColumn(Translation::get(self::WEIGHT)));
    }
}
