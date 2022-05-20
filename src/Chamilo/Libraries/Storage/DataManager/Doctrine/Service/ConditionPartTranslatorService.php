<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionPartTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConditionPartTranslatorService implements ConditionPartTranslatorServiceInterface
{

    protected ConditionPartCache $conditionPartCache;

    protected ConditionPartTranslatorFactory $conditionPartTranslatorFactory;

    private bool $queryCacheEnabled;

    public function __construct(
        ConditionPartTranslatorFactory $conditionPartTranslatorFactory, ConditionPartCache $conditionPartCache,
        ?bool $queryCacheEnabled = true
    )
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
        $this->conditionPartCache = $conditionPartCache;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    public function getConditionPartCache(): ConditionPartCache
    {
        return $this->conditionPartCache;
    }

    public function setConditionPartCache(ConditionPartCache $conditionPartCache): ConditionPartTranslatorService
    {
        $this->conditionPartCache = $conditionPartCache;

        return $this;
    }

    /**
     * @throws \ReflectionException
     */
    protected function getConditionPartTranslator(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart
    ): ConditionPartTranslator
    {
        return $this->getConditionPartTranslatorFactory()->getConditionPartTranslator(
            $this, $dataClassDatabase, $conditionPart
        );
    }

    public function getConditionPartTranslatorFactory(): ConditionPartTranslatorFactory
    {
        return $this->conditionPartTranslatorFactory;
    }

    public function setConditionPartTranslatorFactory(ConditionPartTranslatorFactory $conditionPartTranslatorFactory
    ): ConditionPartTranslatorService
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;

        return $this;
    }

    public function getQueryCacheEnabled(): bool
    {
        return $this->isQueryCacheEnabled();
    }

    public function setQueryCacheEnabled(bool $queryCacheEnabled): ConditionPartTranslatorService
    {
        $this->queryCacheEnabled = $queryCacheEnabled;

        return $this;
    }

    protected function isQueryCacheEnabled(): bool
    {
        return $this->queryCacheEnabled;
    }

    /**
     * @throws \ReflectionException
     */
    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string
    {
        if ($this->isQueryCacheEnabled())
        {
            if (!$this->getConditionPartCache()->exists($conditionPart, $enableAliasing))
            {
                $this->getConditionPartCache()->set(
                    $conditionPart, $enableAliasing,
                    $this->getConditionPartTranslator($dataClassDatabase, $conditionPart)->translate($enableAliasing)
                );
            }

            return $this->getConditionPartCache()->get($conditionPart, $enableAliasing);
        }
        else
        {
            return $this->getConditionPartTranslator($dataClassDatabase, $conditionPart)->translate($enableAliasing);
        }
    }
}