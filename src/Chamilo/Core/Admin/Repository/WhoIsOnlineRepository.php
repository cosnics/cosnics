<?php
namespace Chamilo\Core\Admin\Repository;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Storage\DataClass\Online;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Admin\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WhoIsOnlineRepository
{
    protected ConfigurationConsulter $configurationConsulter;

    protected DataClassRepository $dataClassRepository;

    public function __construct(ConfigurationConsulter $configurationConsulter, DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->configurationConsulter = $configurationConsulter;
    }

    public function createWhoIsOnline(Online $online): bool
    {
        return $this->getDataClassRepository()->create($online);
    }

    /**
     * @return string[]
     */
    public function findDistinctOnlineUserIdentifiers(): array
    {
        $timeLimit = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'timelimit']);

        $pastTime = strtotime(
            '-' . $timeLimit . ' seconds', time()
        );

        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Online::class, Online::PROPERTY_LAST_ACCESS_DATE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($pastTime)
        );

        return $this->getDataClassRepository()->distinct(
            Online::class, new DataClassDistinctParameters(
                $condition,
                new RetrieveProperties([new PropertyConditionVariable(Online::class, Online::PROPERTY_USER_ID)])
            )
        );
    }

    public function findWhoIsOnlineForUserIdentifier(string $userIdentifier): ?Online
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Online::class, Online::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->getDataClassRepository()->retrieve(
            Online::class, new DataClassRetrieveParameters($condition)
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function updateWhoIsOnline(Online $online): bool
    {
        return $this->getDataClassRepository()->update($online);
    }
}