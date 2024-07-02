<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
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

    public function getLastLogin(string $userIdentifier)
    {
        return $this->getLoginLogout($userIdentifier, 'login');
    }

    public function getLastLogout(string $userIdentifier)
    {
        return $this->getLoginLogout($userIdentifier, 'logout');
    }

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
            LoginLogout::class,
            new RetrievesParameters(condition: $condition, count: 1, offset: 0, orderBy: new OrderBy([$order_by]))
        );

        $tracker = $trackers->current();

        if (is_null($tracker))
        {
            return 0;
        }

        return $tracker->get_date();
    }
}
