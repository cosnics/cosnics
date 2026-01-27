<?php
namespace Chamilo\Core\Admin\Storage\Repository;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Storage\DataClass\Online;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\Admin\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OnlineRepository
{
    protected ConfigurationConsulter $configurationConsulter;

    protected DataClassRepository $dataClassRepository;

    public function __construct(ConfigurationConsulter $configurationConsulter, DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createOnline(Online $online): bool
    {
        return $this->getDataClassRepository()->create($online);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
            Online::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Online::class, Online::PROPERTY_USER_ID)]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findOnlineForUserIdentifier(string $userIdentifier): ?Online
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Online::class, Online::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->getDataClassRepository()->retrieve(
            Online::class, new StorageParameters(condition: $condition)
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateOnline(Online $online): bool
    {
        return $this->getDataClassRepository()->update($online);
    }
}