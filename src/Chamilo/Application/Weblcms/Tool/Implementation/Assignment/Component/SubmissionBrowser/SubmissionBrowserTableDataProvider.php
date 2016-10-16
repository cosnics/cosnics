<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser Data provider for a platform group
 *          submissions browser table.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
abstract class SubmissionBrowserTableDataProvider extends RecordTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_properties = $this->get_order_properties($order_property);
        
        return $this->retrieve_from_data_manager(
            $this->get_component()->get_publication_id(), 
            $this->get_component()->get_course_id(), 
            $offset, 
            $count, 
            $order_properties, 
            $condition);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->retrieve_data($condition, null, null)->size();
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the order properties for this data provider
     * 
     * @param ObjectTableOrder[] $order_properties
     *
     * @return ObjectTableOrder[]
     */
    abstract public function get_order_properties($order_properties = array());

    /**
     * Calls the datamanager to retrieve the actual data
     * 
     * @abstract
     *
     *
     *
     *
     *
     *
     *
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
    abstract public function retrieve_from_data_manager($publication_id, $course_id, $offset, $count, $order_properties, 
        $condition);
}
