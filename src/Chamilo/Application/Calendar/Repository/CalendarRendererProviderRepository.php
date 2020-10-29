<?php
namespace Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Calendar\Repository$CalendarRendererProviderRepository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProviderRepository
{

    /**
     *
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Calendar\Storage\DataClass\Visibility>
     * @throws \Exception
     */
    public function findVisibilitiesByUserIdentifier($userIdentifier)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, Visibility::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $condition = new AndCondition($conditions);

        return DataManager::retrieves(Visibility::class, new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param string $source
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Visibility
     * @throws \Exception
     */
    public function findVisibilityBySourceAndUserIdentifier($source, $userIdentifier)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, Visibility::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, Visibility::PROPERTY_SOURCE),
            new StaticConditionVariable($source)
        );
        $condition = new AndCondition($conditions);

        return DataManager::retrieve(Visibility::class, new DataClassRetrieveParameters($condition));
    }
}