<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionPartTranslatorServiceInterface;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionPartTranslator;
use Doctrine\DBAL\Query\QueryBuilder;
use OutOfBoundsException;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionPartTranslatorService implements ConditionPartTranslatorServiceInterface
{
    protected ClassnameUtilities $classnameUtilities;

    protected ConditionPartCache $conditionPartCache;

    /**
     * @var \Chamilo\Libraries\Storage\Query\ConditionPartTranslator[]
     */
    protected array $conditionPartTranslators = [];

    protected bool $queryCacheEnabled;

    public function __construct(ConditionPartCache $conditionPartCache, ?bool $queryCacheEnabled = true)
    {
        $this->conditionPartCache = $conditionPartCache;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    public function addConditionPartTranslator(ConditionPartTranslator $conditionPartTranslator): void
    {
        $this->conditionPartTranslators[$conditionPartTranslator->getConditionClass()] = $conditionPartTranslator;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConditionPartCache(): ConditionPartCache
    {
        return $this->conditionPartCache;
    }

    public function getConditionPartTranslator(ConditionPart $conditionPart): ConditionPartTranslator
    {
        $conditionPartTranslatorType = get_class($conditionPart);

        if (!array_key_exists($conditionPartTranslatorType, $this->conditionPartTranslators))
        {
            throw new OutOfBoundsException($conditionPartTranslatorType . ' has no valid ConditionPartTranslator');
        }

        return $this->conditionPartTranslators[$conditionPartTranslatorType];
    }

    public function isQueryCacheEnabled(): bool
    {
        return $this->queryCacheEnabled;
    }

    public function translate(
        QueryBuilder $queryBuilder, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string
    {
//        if ($this->isQueryCacheEnabled())
//        {
//            return $this->getConditionPartCache()->add(
//                $conditionPart, $enableAliasing, function () use ($queryBuilder, $conditionPart, $enableAliasing) {
//                return $this->translateConditionPart($queryBuilder, $conditionPart, $enableAliasing);
//            }
//            );
//        }
//        else
//        {
            return $this->translateConditionPart($queryBuilder, $conditionPart, $enableAliasing);
//        }
    }

    private function translateConditionPart(
        QueryBuilder $queryBuilder, ConditionPart $conditionPart, ?bool $enableAliasing
    ): string
    {
        return $this->getConditionPartTranslator($conditionPart)->translate(
            $queryBuilder, $conditionPart, $enableAliasing
        );
    }

}