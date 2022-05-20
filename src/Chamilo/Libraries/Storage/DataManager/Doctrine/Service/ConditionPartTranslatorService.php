<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\CaseConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\CaseElementConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\ComparisonConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\DateFormatConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\DistinctConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\FunctionConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\InConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\MultipleAggregateConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\NotConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\OperationConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PatternMatchConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PropertiesConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PropertyConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\RegularExpressionConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\StaticConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\SubselectConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Condition\RegularExpressionCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\DateFormatConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use UnexpectedValueException;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConditionPartTranslatorService implements ConditionPartTranslatorServiceInterface
{

    protected CaseConditionVariableTranslator $caseConditionVariableTranslator;

    protected CaseElementConditionVariableTranslator $caseElementConditionVariableTranslator;

    protected ComparisonConditionTranslator $comparisonConditionTranslator;

    protected ConditionPartCache $conditionPartCache;

    protected DateFormatConditionVariableTranslator $dateFormatConditionVariableTranslator;

    protected DistinctConditionVariableTranslator $distinctConditionVariableTranslator;

    protected FunctionConditionVariableTranslator $functionConditionVariableTranslator;

    protected InConditionTranslator $inConditionTranslator;

    protected MultipleAggregateConditionTranslator $multipleAggregateConditionTranslator;

    protected NotConditionTranslator $notConditionTranslator;

    protected OperationConditionVariableTranslator $operationConditionVariableTranslator;

    protected PatternMatchConditionTranslator $patternMatchConditionTranslator;

    protected PropertiesConditionVariableTranslator $propertiesConditionVariableTranslator;

    protected PropertyConditionVariableTranslator $propertyConditionVariableTranslator;

    protected bool $queryCacheEnabled;

    protected RegularExpressionConditionTranslator $regularExpressionConditionTranslator;

    protected StaticConditionVariableTranslator $staticConditionVariableTranslator;

    protected SubselectConditionTranslator $subselectConditionTranslator;

    public function __construct(
        CaseConditionVariableTranslator $caseConditionVariableTranslator,
        CaseElementConditionVariableTranslator $caseElementConditionVariableTranslator,
        ComparisonConditionTranslator $comparisonConditionTranslator,
        DateFormatConditionVariableTranslator $dateFormatConditionVariableTranslator,
        DistinctConditionVariableTranslator $distinctConditionVariableTranslator,
        FunctionConditionVariableTranslator $functionConditionVariableTranslator,
        InConditionTranslator $inConditionTranslator,
        MultipleAggregateConditionTranslator $multipleAggregateConditionTranslator,
        NotConditionTranslator $notConditionTranslator,
        OperationConditionVariableTranslator $operationConditionVariableTranslator,
        PatternMatchConditionTranslator $patternMatchConditionTranslator,
        PropertiesConditionVariableTranslator $propertiesConditionVariableTranslator,
        PropertyConditionVariableTranslator $propertyConditionVariableTranslator,
        RegularExpressionConditionTranslator $regularExpressionConditionTranslator,
        StaticConditionVariableTranslator $staticConditionVariableTranslator,
        SubselectConditionTranslator $subselectConditionTranslator, ConditionPartCache $conditionPartCache,
        ?bool $queryCacheEnabled = true
    )
    {
        $this->caseConditionVariableTranslator = $caseConditionVariableTranslator;
        $this->caseElementConditionVariableTranslator = $caseElementConditionVariableTranslator;
        $this->comparisonConditionTranslator = $comparisonConditionTranslator;
        $this->dateFormatConditionVariableTranslator = $dateFormatConditionVariableTranslator;
        $this->distinctConditionVariableTranslator = $distinctConditionVariableTranslator;
        $this->functionConditionVariableTranslator = $functionConditionVariableTranslator;
        $this->inConditionTranslator = $inConditionTranslator;
        $this->multipleAggregateConditionTranslator = $multipleAggregateConditionTranslator;
        $this->notConditionTranslator = $notConditionTranslator;
        $this->operationConditionVariableTranslator = $operationConditionVariableTranslator;
        $this->patternMatchConditionTranslator = $patternMatchConditionTranslator;
        $this->propertiesConditionVariableTranslator = $propertiesConditionVariableTranslator;
        $this->propertyConditionVariableTranslator = $propertyConditionVariableTranslator;
        $this->regularExpressionConditionTranslator = $regularExpressionConditionTranslator;
        $this->staticConditionVariableTranslator = $staticConditionVariableTranslator;
        $this->subselectConditionTranslator = $subselectConditionTranslator;
        $this->conditionPartCache = $conditionPartCache;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    public function getCaseConditionVariableTranslator(): CaseConditionVariableTranslator
    {
        return $this->caseConditionVariableTranslator;
    }

    public function setCaseConditionVariableTranslator(CaseConditionVariableTranslator $caseConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->caseConditionVariableTranslator = $caseConditionVariableTranslator;

        return $this;
    }

    public function getCaseElementConditionVariableTranslator(): CaseElementConditionVariableTranslator
    {
        return $this->caseElementConditionVariableTranslator;
    }

    public function setCaseElementConditionVariableTranslator(
        CaseElementConditionVariableTranslator $caseElementConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->caseElementConditionVariableTranslator = $caseElementConditionVariableTranslator;

        return $this;
    }

    public function getComparisonConditionTranslator(): ComparisonConditionTranslator
    {
        return $this->comparisonConditionTranslator;
    }

    public function setComparisonConditionTranslator(ComparisonConditionTranslator $comparisonConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->comparisonConditionTranslator = $comparisonConditionTranslator;

        return $this;
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

    public function getDateFormatConditionVariableTranslator(): DateFormatConditionVariableTranslator
    {
        return $this->dateFormatConditionVariableTranslator;
    }

    public function setDateFormatConditionVariableTranslator(
        DateFormatConditionVariableTranslator $dateFormatConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->dateFormatConditionVariableTranslator = $dateFormatConditionVariableTranslator;

        return $this;
    }

    public function getDistinctConditionVariableTranslator(): DistinctConditionVariableTranslator
    {
        return $this->distinctConditionVariableTranslator;
    }

    public function setDistinctConditionVariableTranslator(
        DistinctConditionVariableTranslator $distinctConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->distinctConditionVariableTranslator = $distinctConditionVariableTranslator;

        return $this;
    }

    public function getFunctionConditionVariableTranslator(): FunctionConditionVariableTranslator
    {
        return $this->functionConditionVariableTranslator;
    }

    public function setFunctionConditionVariableTranslator(
        FunctionConditionVariableTranslator $functionConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->functionConditionVariableTranslator = $functionConditionVariableTranslator;

        return $this;
    }

    public function getInConditionTranslator(): InConditionTranslator
    {
        return $this->inConditionTranslator;
    }

    public function setInConditionTranslator(InConditionTranslator $inConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->inConditionTranslator = $inConditionTranslator;

        return $this;
    }

    public function getMultipleAggregateConditionTranslator(): MultipleAggregateConditionTranslator
    {
        return $this->multipleAggregateConditionTranslator;
    }

    public function setMultipleAggregateConditionTranslator(
        MultipleAggregateConditionTranslator $multipleAggregateConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->multipleAggregateConditionTranslator = $multipleAggregateConditionTranslator;

        return $this;
    }

    public function getNotConditionTranslator(): NotConditionTranslator
    {
        return $this->notConditionTranslator;
    }

    public function setNotConditionTranslator(NotConditionTranslator $notConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->notConditionTranslator = $notConditionTranslator;

        return $this;
    }

    public function getOperationConditionVariableTranslator(): OperationConditionVariableTranslator
    {
        return $this->operationConditionVariableTranslator;
    }

    public function setOperationConditionVariableTranslator(
        OperationConditionVariableTranslator $operationConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->operationConditionVariableTranslator = $operationConditionVariableTranslator;

        return $this;
    }

    public function getPatternMatchConditionTranslator(): PatternMatchConditionTranslator
    {
        return $this->patternMatchConditionTranslator;
    }

    public function setPatternMatchConditionTranslator(PatternMatchConditionTranslator $patternMatchConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->patternMatchConditionTranslator = $patternMatchConditionTranslator;

        return $this;
    }

    public function getPropertiesConditionVariableTranslator(): PropertiesConditionVariableTranslator
    {
        return $this->propertiesConditionVariableTranslator;
    }

    public function setPropertiesConditionVariableTranslator(
        PropertiesConditionVariableTranslator $propertiesConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->propertiesConditionVariableTranslator = $propertiesConditionVariableTranslator;

        return $this;
    }

    public function getPropertyConditionVariableTranslator(): PropertyConditionVariableTranslator
    {
        return $this->propertyConditionVariableTranslator;
    }

    public function setPropertyConditionVariableTranslator(
        PropertyConditionVariableTranslator $propertyConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->propertyConditionVariableTranslator = $propertyConditionVariableTranslator;

        return $this;
    }

    public function getRegularExpressionConditionTranslator(): RegularExpressionConditionTranslator
    {
        return $this->regularExpressionConditionTranslator;
    }

    public function setRegularExpressionConditionTranslator(
        RegularExpressionConditionTranslator $regularExpressionConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->regularExpressionConditionTranslator = $regularExpressionConditionTranslator;

        return $this;
    }

    public function getStaticConditionVariableTranslator(): StaticConditionVariableTranslator
    {
        return $this->staticConditionVariableTranslator;
    }

    public function setStaticConditionVariableTranslator(
        StaticConditionVariableTranslator $staticConditionVariableTranslator
    ): ConditionPartTranslatorService
    {
        $this->staticConditionVariableTranslator = $staticConditionVariableTranslator;

        return $this;
    }

    public function getSubselectConditionTranslator(): SubselectConditionTranslator
    {
        return $this->subselectConditionTranslator;
    }

    public function setSubselectConditionTranslator(SubselectConditionTranslator $subselectConditionTranslator
    ): ConditionPartTranslatorService
    {
        $this->subselectConditionTranslator = $subselectConditionTranslator;

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

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function translateConditionPart(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, ?bool $enableAliasing
    ): string
    {
        if ($conditionPart instanceof PropertyConditionVariable)
        {
            return $this->getPropertyConditionVariableTranslator()->translate(
                $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof ComparisonCondition)
        {
            return $this->getComparisonConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof StaticConditionVariable)
        {
            return $this->getStaticConditionVariableTranslator()->translate(
                $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof MultipleAggregateCondition)
        {
            return $this->getMultipleAggregateConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof InCondition)
        {
            return $this->getInConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof PatternMatchCondition)
        {
            return $this->getPatternMatchConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof NotCondition)
        {
            return $this->getNotConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof FunctionConditionVariable)
        {
            return $this->getFunctionConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof PropertiesConditionVariable)
        {
            return $this->getPropertiesConditionVariableTranslator()->translate($conditionPart, $enableAliasing);
        }

        if ($conditionPart instanceof SubselectCondition)
        {
            return $this->getSubselectConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof OperationConditionVariable)
        {
            return $this->getOperationConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof DistinctConditionVariable)
        {
            return $this->getDistinctConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof RegularExpressionCondition)
        {
            return $this->getRegularExpressionConditionTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof CaseElementConditionVariable)
        {
            return $this->getCaseElementConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof CaseConditionVariable)
        {
            return $this->getCaseConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        if ($conditionPart instanceof DateFormatConditionVariable)
        {
            return $this->getDateFormatConditionVariableTranslator()->translate(
                $this, $dataClassDatabase, $conditionPart, $enableAliasing
            );
        }

        throw new UnexpectedValueException('Unknown condition type: ' . get_class($conditionPart));
    }

}