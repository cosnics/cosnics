<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\ObjectTableOrder;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Description of submitter_group_submissions_browser_table_data_provider
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmitterGroupSubmissionsTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Retrieves Submissions from the database.
     * WARNING: This function circumvents the tracking model. REASON: The table
     * construction model is based on result sets retrieved straight from the database whereas the tracking model is
     * based on arrays.
     *
     * @param $offset int the start point in the list
     * @param $count int the number of objects to be retrieved from the database.
     * @param $order_property ObjectTableOrder the way the contents of the table should be ordered.
     */
    public function retrieve_data($condition, $offset = null, $count = null, $order_property = null)
    {
        if (is_null($order_property))
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(
                    AssignmentSubmission :: class_name(),
                    AssignmentSubmission :: PROPERTY_DATE_SUBMITTED));
        }
        // From here on, the tracking model is circumvented.
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
