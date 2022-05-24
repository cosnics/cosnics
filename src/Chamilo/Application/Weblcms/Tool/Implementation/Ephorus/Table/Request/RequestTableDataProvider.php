<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Data provider for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getRequestManager()->countRequestsWithContentObjects($condition);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager
     */
    public function getRequestManager()
    {
        return $this->get_component()->getRequestManager();
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        if ($orderBy == null)
        {
            $orderBy = new OrderBy(array(
                new OrderProperty(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)
                )
            ));
        }

        return $this->getRequestManager()->findRequestsWithContentObjects(
            new RecordRetrievesParameters(null, $condition, $count, $offset, $orderBy)
        );
    }
}
