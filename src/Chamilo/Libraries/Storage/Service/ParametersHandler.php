<?php

namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassBasicRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class ParametersHandler
{

    protected function handleCompositeDataClassJoins(string $dataClassName, DataClassParameters $parameters
    ): ParametersHandler
    {
        if ($parameters->getJoins() instanceof Joins)
        {
            $dataClassProperties = $parameters->getDataClassProperties();

            foreach ($parameters->getJoins()->get() as $join)
            {
                if (is_subclass_of($join->getDataClassName(), CompositeDataClass::class))
                {
                    if (is_subclass_of($dataClassName, $join->getDataClassName()))
                    {
                        $dataClassProperties->add(new PropertiesConditionVariable($join->getDataClassName()));
                    }
                }
            }
        }

        return $this;
    }

    public function handleDataClassCountGroupedParameters(DataClassCountGroupedParameters $parameters
    ): ParametersHandler
    {
        $dataClassProperties = $parameters->getDataClassProperties();
        $dataClassProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $this;
    }

    public function handleDataClassCountParameters(DataClassCountParameters $parameters): ParametersHandler
    {
        $dataClassProperties = $parameters->getDataClassProperties();

        if ($dataClassProperties instanceof DataClassProperties)
        {
            $dataClassPropertyVariable = $dataClassProperties->getFirst();
        }
        else
        {
            $dataClassPropertyVariable = new StaticConditionVariable(1);
        }

        $countVariable = new FunctionConditionVariable(FunctionConditionVariable::COUNT, $dataClassPropertyVariable);

        $parameters->setDataClassProperties(new DataClassProperties(array($countVariable)));

        return $this;
    }

    public function handleDataClassDistinctParameters(DataClassDistinctParameters $parameters): ParametersHandler
    {
        $existingConditionVariables = $parameters->getDataClassProperties()->get();

        $parameters->setDataClassProperties(
            new DataClassProperties(array(new DistinctConditionVariable($existingConditionVariables)))
        );

        return $this;
    }

    public function handleDataClassRetrieveParameters(string $dataClassName, DataClassRetrieveParameters $parameters
    ): ParametersHandler
    {
        $this->handleDataClassRetrievesParameters($dataClassName, $parameters);

        $parameters->setCount(1);
        $parameters->setOffset(0);

        return $this;
    }

    public function handleDataClassRetrievesParameters(
        string $dataClassName, DataClassBasicRetrieveParameters $parameters
    ): ParametersHandler
    {
        $this->setDataClassPropertiesClassName($dataClassName, $parameters);
        $this->handleCompositeDataClassJoins($dataClassName, $parameters);

        return $this;
    }

    public function setDataClassPropertiesClassName(
        string $dataClassName, DataClassParameters $dataClassRetrieveParameters
    ): ParametersHandler
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::isExtended())
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::isExtended())
        {
            $propertiesClassName = $dataClassName::parentClassName();
        }
        else
        {
            $propertiesClassName = $dataClassName;
        }

        $dataClassRetrieveParameters->setDataClassProperties(
            new DataClassProperties(
                array(new PropertiesConditionVariable($propertiesClassName))
            )
        );

        return $this;
    }
}