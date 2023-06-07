<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionPartTranslator;
use OutOfBoundsException;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Service
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

    private function translateConditionPart(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, ?bool $enableAliasing
    ): string
    {
        return $this->getConditionPartTranslator($conditionPart)->translate(
            $dataClassDatabase, $conditionPart, $enableAliasing
        );
    }

}