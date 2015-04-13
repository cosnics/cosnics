<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Description of submitter_user_submissions_browser_table_data_provider
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmitterUserSubmissionsTableDataProvider extends DataClassTableDataProvider
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
        // From here on, the tracking model is circumvented.
        $data = AssignmentSubmission :: get_data(
            AssignmentSubmission :: CLASS_NAME, 
            \Chamilo\Application\Weblcms\Manager :: APPLICATION_NAME, 
            $condition, 
            $offset, 
            $count, 
            $order_property);
        return $data;
    }

    public function count_data($condition)
    {
        return $this->retrieve_data()->size();
    }
}
