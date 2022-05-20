<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Service;

use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
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

    protected ConditionPartCache $conditionPartCache;

    protected bool $queryCacheEnabled;

    public function __construct(ConditionPartCache $conditionPartCache, ?bool $queryCacheEnabled = true)
    {
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

    public function isQueryCacheEnabled(): bool
    {
        return $this->queryCacheEnabled;
    }

    public function setQueryCacheEnabled(bool $queryCacheEnabled): ConditionPartTranslatorService
    {
        $this->queryCacheEnabled = $queryCacheEnabled;

        return $this;
    }

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
                    $this->translateConditionPart($dataClassDatabase, $conditionPart, $enableAliasing)
                );
            }

            return $this->getConditionPartCache()->get($conditionPart, $enableAliasing);
        }
        else
        {
            return $this->translateConditionPart($dataClassDatabase, $conditionPart, $enableAliasing);
        }
    }
}