<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Service;

use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionPartTranslatorService implements ConditionPartTranslatorServiceInterface
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\AdoDb\Factory\ConditionPartTranslatorFactory
     *     $conditionPartTranslatorFactory
     */
    protected $conditionPartTranslatorFactory;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionPartCache
     */
    protected $conditionPartCache;

    /**
     *
     * @var boolean
     */
    private $queryCacheEnabled;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Factory\ConditionPartTranslatorFactory $conditionPartTranslatorFactory
     * @param \Chamilo\Libraries\Storage\Cache\ConditionPartCache $conditionPartCache
     * @param boolean $queryCacheEnabled
     */
    public function __construct(
        ConditionPartTranslatorFactory $conditionPartTranslatorFactory, ConditionPartCache $conditionPartCache,
        $queryCacheEnabled = true
    )
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
        $this->conditionPartCache = $conditionPartCache;
        $this->queryCacheEnabled = $queryCacheEnabled;
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
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     *
     * @return \Chamilo\Libraries\Storage\Query\ConditionPartTranslator
     */
    protected function getConditionPartTranslator(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart
    )
    {
        return $this->getConditionPartTranslatorFactory()->getConditionPartTranslator(
            $this, $dataClassDatabase, $conditionPart
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Factory\ConditionPartTranslatorFactory
     */
    public function getConditionPartTranslatorFactory()
    {
        return $this->conditionPartTranslatorFactory;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Factory\ConditionPartTranslatorFactory $conditionPartTranslatorFactory
     */
    public function setConditionPartTranslatorFactory(ConditionPartTranslatorFactory $conditionPartTranslatorFactory)
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
    }

    /**
     *
     * @return boolean
     */
    public function getQueryCacheEnabled()
    {
        return $this->queryCacheEnabled;
    }

    /**
     *
     * @return boolean
     */
    protected function isQueryCacheEnabled()
    {
        return (bool) $this->getQueryCacheEnabled();
    }

    /**
     *
     * @param boolean $queryCacheEnabled
     */
    public function setQueryCacheEnabled($queryCacheEnabled)
    {
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     *
     * @return string
     */
    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, bool $enableAliasing = true
    )
    {
        if ($this->isQueryCacheEnabled())
        {
            if (!$this->getConditionPartCache()->exists($conditionPart))
            {
                $this->getConditionPartCache()->set(
                    $conditionPart,
                    $this->getConditionPartTranslator($dataClassDatabase, $conditionPart)->translate($enableAliasing)
                );
            }

            return $this->getConditionPartCache()->get($conditionPart);
        }
        else
        {
            return $this->getConditionPartTranslator($conditionPart)->translate($enableAliasing);
        }
    }
}