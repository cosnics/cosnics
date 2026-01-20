<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->create($setting);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->delete($setting);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findSettingByContextAndVariableName(string $context, string $variable): ?Setting
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_CONTEXT),
            new StaticConditionVariable($context)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE),
            new StaticConditionVariable($variable)
        );

        return $this->getDataClassRepository()->retrieve(
            Setting::class, new StorageParameters(condition: new AndCondition($conditions))
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSettingContextsForCondition(?Condition $condition = null): array
    {
        return $this->getDataClassRepository()->distinct(
            Setting::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Setting::class, Setting::PROPERTY_CONTEXT)]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSettingsAsRecords(): ArrayCollection
    {
        return $this->getDataClassRepository()->records(
            Setting::class, new StorageParameters(
                retrieveProperties: new RetrieveProperties([new PropertiesConditionVariable(Setting::class)])
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->update($setting);
    }
}