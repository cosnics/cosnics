<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Column model for ephorus requests browser table.
 * 
 * @author Tom Goethals
 */
class AssignmentRequestTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{
    const COLUMN_NAME_AUTHOR = 'author';

    /**
     * Gets the column names to use in the table.
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION));
        $this->add_column(new StaticTableColumn(self::COLUMN_NAME_AUTHOR));
        $this->add_column(
            new DataClassPropertyTableColumn(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_TYPE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_DATE_SUBMITTED));
        $this->add_column(new DataClassPropertyTableColumn(Request::class_name(), Request::PROPERTY_REQUEST_TIME));
        $this->add_column(new DataClassPropertyTableColumn(Request::class_name(), Request::PROPERTY_PERCENTAGE));
        $this->add_column(new DataClassPropertyTableColumn(Request::class_name(), Request::PROPERTY_STATUS));
        $this->add_column(
            new DataClassPropertyTableColumn(Request::class_name(), Request::PROPERTY_VISIBLE_IN_INDEX));
    }
}
