<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionBrowserTableDataProvider;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser Data provider for a submitters browser
 *          table.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class SubmissionUsersBrowserTableDataProvider extends SubmissionBrowserTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the order properties for this data provider
     * 
     * @param ObjectTableOrder[] $order_properties
     *
     * @return ObjectTableOrder[]
     */
    public function get_order_properties($order_properties = array())
    {
        $order_properties[] = new OrderBy(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), 
            SORT_ASC);
        $order_properties[] = new OrderBy(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), 
            SORT_ASC);
        
        return $order_properties;
    }

    /**
     * Calls the datamanager to retrieve the actual data
     * 
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_properties
     * @param Condition $condition
     *
     * @return RecordResultSet
     */
    public function retrieve_from_data_manager($publication_id, $course_id, $offset, $count, $order_properties, 
        $condition)
    {
        return DataManager::retrieve_assignment_publication_target_users(
            $publication_id, 
            $course_id, 
            $offset, 
            $count, 
            $order_properties, 
            $condition);
    }
}
