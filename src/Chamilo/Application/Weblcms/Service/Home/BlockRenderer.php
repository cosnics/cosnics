<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 */
abstract class BlockRenderer extends \Chamilo\Core\Home\Renderer\BlockRenderer
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getLastLogin(string $userIdentifier)
    {
        return $this->getLoginLogout($userIdentifier, 'login');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getLastLogout(string $userIdentifier)
    {
        return $this->getLoginLogout($userIdentifier, 'logout');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getLoginLogout(string $userIdentifier, $type)
    {
        $order_by = new OrderProperty(
            new PropertyConditionVariable(
                LoginLogout::class, LoginLogout::PROPERTY_DATE
            )
        );

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LoginLogout::class, LoginLogout::PROPERTY_USER_ID
            ), new StaticConditionVariable($userIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LoginLogout::class, LoginLogout::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);

        $trackers = DataManager::retrieves(
            LoginLogout::class, new DataClassRetrievesParameters($condition, 1, 0, new OrderBy([$order_by]))
        );

        $tracker = $trackers->current();

        if (is_null($tracker))
        {
            return 0;
        }

        return $tracker->get_date();
    }
}
