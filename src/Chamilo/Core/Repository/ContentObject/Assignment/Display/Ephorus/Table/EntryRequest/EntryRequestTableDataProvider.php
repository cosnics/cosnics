<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequest;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Data provider for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryRequestTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        if (is_null($orderBy))
        {
            $orderBy = new OrderBy(array(
                new OrderProperty(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)
                )
            ));
        }

        return $this->getDataProvider()->findAssignmentEntriesWithEphorusRequests(
            new RecordRetrievesParameters(null, $condition, $count, $offset, $orderBy)
        );
    }
}
