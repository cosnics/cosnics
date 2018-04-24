<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequest;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Data provider for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryRequestTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the objects to display in the table.
     * For now, objects are composed in the code itself from several source
     * objects.
     *
     * @param $offset
     * @param $count
     * @param null $order_property
     *
     * @return mixed
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)
            );
        }

        return $this->getDataProvider()->findAssignmentEntriesWithEphorusRequests(
            new RecordRetrievesParameters(null, $condition, $count, $offset, $order_property)
        );
    }

    /**
     * Returns the count of the objects
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getDataProvider()->countAssignmentEntriesWithEphorusRequests($condition);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface
     */
    public function getDataProvider()
    {
        return $this->get_component()->getDataProvider();
    }
}
