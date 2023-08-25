<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Repository
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

    public function createSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->create($setting);
    }

    public function deleteSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->delete($setting);
    }

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
            Setting::class, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSettingsAsRecords(): ArrayCollection
    {
        return $this->getDataClassRepository()->records(
            Setting::class, new RecordRetrievesParameters(
                new RetrieveProperties([new PropertiesConditionVariable(Setting::class)])
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function updateSetting(Setting $setting): bool
    {
        return $this->getDataClassRepository()->update($setting);
    }
}