<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Description of student_submissions_browser_own_groups_table_data_provider
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class StudentSubmissionsOwnGroupsTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset = null, $count = null, $order_property = null)
    {
        if (is_null($order_property))
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(
                    AssignmentSubmission :: class_name(),
                    AssignmentSubmission :: PROPERTY_DATE_SUBMITTED));
        }
        return AssignmentSubmission :: get_data(
            AssignmentSubmission :: class_name(),
            null,
            $condition,
            $offset,
            $count,
            $order_property);
    }

    public function count_data($condition)
    {
        return $this->retrieve_data($condition)->size();
    }
}
