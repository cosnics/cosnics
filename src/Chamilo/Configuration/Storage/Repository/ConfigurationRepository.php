<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;

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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findSettingsAsRecords()
    {
        return $this->getDataClassRepository()->records(
            Setting::class_name(),
            new RecordRetrievesParameters(
                new DataClassProperties(array(new PropertiesConditionVariable(Setting::class)))));
    }

    /**
     *
     * @return boolean
     */
    public function clearSettingCache()
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(Setting::class_name());
    }
}