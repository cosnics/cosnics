<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionCourseGroupBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser\SubmissionGroupsBrowserTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Extends SubmissionBrowserTableColumnModel to include the group members
 * column.
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubmissionCourseGroupsBrowserTableColumnModel extends SubmissionGroupsBrowserTableColumnModel
{

    /**
     * Adds a column for the group name
     */
    protected function add_group_name_column()
    {
        $this->add_column(new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME));
    }
}
