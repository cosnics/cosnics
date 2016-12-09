<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the submitter submissions browser table.
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmitterUserSubmissionsTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{
    const PROPERTY_PUBLICATION_TITLE = 'PublicationTitle';
    const PROPERTY_CONTENT_OBJECT_DESCRIPTION = 'ContentObjectDescription';

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self::PROPERTY_PUBLICATION_TITLE));
        $this->add_column(new StaticTableColumn(self::PROPERTY_CONTENT_OBJECT_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED));
        $this->add_column(
            new StaticTableColumn(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SCORE));
        $this->add_column(new StaticTableColumn(Manager::PROPERTY_NUMBER_OF_FEEDBACKS));
    }
}
