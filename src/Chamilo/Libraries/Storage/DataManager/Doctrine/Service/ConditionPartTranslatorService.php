<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\Query\ConditionPart;

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
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

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
     * @param \Chamilo\Configuration\Configuration $configuration
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionTranslatorFactory $conditionPartTranslatorFactory
     * @param \Chamilo\Libraries\Storage\Cache\ConditionPartCache $conditionPartCache
     */
    public function __construct(Configuration $configuration,
        ConditionPartTranslatorFactory $conditionPartTranslatorFactory, ConditionPartCache $conditionPartCache)
    {
        $this->configuration = $configuration;
        $this->conditionTranslatorFactory = $conditionPartTranslatorFactory;
        $this->conditionPartCache = $conditionPartCache;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
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
        return (bool) $this->getConfiguration()->get_setting(
            array('Chamilo\Configuration', 'debug', 'enable_query_cache'));
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator
     */
    protected function getConditionPartTranslator(ConditionPart $conditionPart)
    {
        return $this->getConditionPartTranslatorFactory()->getConditionPartTranslator($this, $conditionPart);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $condition
     */
    public function translateConditionPart(ConditionPart $conditionPart)
    {
        if ($this->isQueryCacheEnabled())
        {
            if (! $this->getConditionPartCache()->exists($conditionPart))
            {
                $this->getConditionPartCache()->set(
                    $conditionPart,
                    $this->getConditionPartTranslator($conditionPart)->translate());
            }

            return $this->getConditionPartCache()->get($conditionPart);
        }
        else
        {
            return $this->getConditionPartTranslator($conditionPart)->translate();
        }
    }
}