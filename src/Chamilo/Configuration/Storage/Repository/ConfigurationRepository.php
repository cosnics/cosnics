<?php

namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Configuration\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findSettingsAsRecords()
    {
        return $this->getDataClassRepository()->records(Setting::class_name(), new RecordRetrievesParameters());
    }

    /**
     *
     * @return boolean
     */
    public function clearSettingCache()
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(Setting::class_name());
    }

    /**
     * @param string $context
     * @param string $variable
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findSettingByContextAndVariable(string $context, string $variable)
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

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(Setting::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     *
     * @return bool
     */
    public function createSetting(Setting $setting)
    {
        return $this->dataClassRepository->create($setting);
    }

    /**
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     *
     * @return bool
     */
    public function updateSetting(Setting $setting)
    {
        return $this->dataClassRepository->update($setting);
    }
}