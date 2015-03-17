<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionBrowserTableColumnModel;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Describes the table column model of the submission users browser table
 * 
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubmissionUsersBrowserTableColumnModel extends SubmissionBrowserTableColumnModel
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        
        parent :: initialize_columns();
    }
}
