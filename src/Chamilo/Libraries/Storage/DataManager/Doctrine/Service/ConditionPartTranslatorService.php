<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConditionPartTranslatorService
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationService
     */
    protected $configurationService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionTranslatorFactory $conditionPartTranslatorFactory
     */
    protected $conditionPartTranslatorFactory;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionPartCache
     */
    protected $conditionPartCache;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionTranslatorFactory $conditionPartTranslatorFactory
     * @param \Chamilo\Libraries\Storage\Cache\ConditionPartCache $conditionPartCache
     */
    public function __construct(ConfigurationService $configurationService,
        ConditionPartTranslatorFactory $conditionPartTranslatorFactory, ConditionPartCache $conditionPartCache)
    {
        $this->configurationService = $configurationService;
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
        $this->conditionPartCache = $conditionPartCache;
    }

    /**
     *
     * @return Chamilo\Configuration\Service\ConfigurationService
     */
    public function getConfigurationService()
    {
        return $this->configurationService;
    }

    /**
     *
     * @param Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function setConfigurationService($configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionPartCache
     */
    public function getConditionPartCache()
    {
        return $this->conditionPartCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\ConditionPartCache $conditionPartCache
     */
    public function setConditionPartCache($conditionPartCache)
    {
        $this->conditionPartCache = $conditionPartCache;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory
     */
    public function getConditionPartTranslatorFactory()
    {
        return $this->conditionPartTranslatorFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory $conditionTranslatorFactory
     */
    public function setConditionPartTranslatorFactory(ConditionPartTranslatorFactory $conditionPartTranslatorFactory)
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
    }

    /**
     *
     * @return boolean
     */
    protected function isQueryCacheEnabled()
    {
        return (bool) $this->getConfigurationService()->getSetting(
            array('Chamilo\Configuration', 'debug', 'enable_query_cache'));
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator
     */
    protected function getConditionPartTranslator(DataClassDatabaseInterface $dataClassDatabase,
        ConditionPart $conditionPart)
    {
        return $this->getConditionPartTranslatorFactory()->getConditionPartTranslator(
            $this,
            $dataClassDatabase,
            $conditionPart);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @return string
     */
    public function translateConditionPart(DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart)
    {
        if ($this->isQueryCacheEnabled())
        {
            if (! $this->getConditionPartCache()->exists($conditionPart))
            {
                $this->getConditionPartCache()->set(
                    $conditionPart,
                    $this->getConditionPartTranslator($dataClassDatabase, $conditionPart)->translate());
            }

            return $this->getConditionPartCache()->get($conditionPart);
        }
        else
        {
            return $this->getConditionPartTranslator($conditionPart)->translate();
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    public function translateCondition(DataClassDatabaseInterface $dataClassDatabase, Condition $condition)
    {
        return $this->translateConditionPart($dataClassDatabase, $condition);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return string
     */
    public function translateConditionVariable(DataClassDatabaseInterface $dataClassDatabase,
        ConditionVariable $conditionVariable)
    {
        return $this->translateConditionPart($dataClassDatabase, $conditionVariable);
    }
}