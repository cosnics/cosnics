<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Abstract table column model for the submitters browser table
 * 
 * @package application.weblcms.tool.assignment.php.component.submission_browser
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class SubmissionBrowserTableColumnModel extends RecordTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new TableColumn(Manager :: PROPERTY_FIRST_SUBMISSION, null, true, ''));
        $this->add_column(new TableColumn(Manager :: PROPERTY_LAST_SUBMISSION, null, true, ''));
        $this->add_column(new TableColumn(Manager :: PROPERTY_NUMBER_OF_SUBMISSIONS, null, true, ''));
        $this->add_column(new TableColumn(Manager :: PROPERTY_NUMBER_OF_FEEDBACKS, null, false, ''));
    }
}
