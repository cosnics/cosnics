<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\AssessmentResults;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Defines the columns for the table.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssessmentResultsTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(Translation::get('User')));
        $this->add_column(new StaticTableColumn(Translation::get('Date')));
        $this->add_column(new StaticTableColumn(Translation::get('Score')));
        $this->add_column(new StaticTableColumn(Translation::get('Time')));
    }
}
