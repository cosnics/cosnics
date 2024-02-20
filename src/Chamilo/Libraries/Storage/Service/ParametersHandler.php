<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Parameters\DataClassBasicRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class ParametersHandler
{
    public function handleDataClassCountGroupedParameters(DataClassCountGroupedParameters $parameters
    ): ParametersHandler
    {
        $retrieveProperties = $parameters->getRetrieveProperties();
        $retrieveProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $this;
    }

    public function handleDataClassCountParameters(DataClassCountParameters $parameters): ParametersHandler
    {
        $retrieveProperties = $parameters->getRetrieveProperties();

        if ($retrieveProperties instanceof RetrieveProperties)
        {
            $dataClassPropertyVariable = $retrieveProperties->getFirst();
        }
        else
        {
            $dataClassPropertyVariable = new StaticConditionVariable(1);
        }

        $countVariable = new FunctionConditionVariable(FunctionConditionVariable::COUNT, $dataClassPropertyVariable);

        $parameters->setRetrieveProperties(new RetrieveProperties([$countVariable]));

        return $this;
    }

    public function handleDataClassDistinctParameters(DataClassDistinctParameters $parameters): ParametersHandler
    {
        $existingConditionVariables = $parameters->getRetrieveProperties()->get();

        $parameters->setRetrieveProperties(
            new RetrieveProperties([new DistinctConditionVariable($existingConditionVariables)])
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
        if (!$parameters->getRetrieveProperties() instanceof RetrieveProperties)
        {
            $this->setDataClassPropertiesClassName($dataClassName, $parameters);
        }

        return $this;
    }

    public function setDataClassPropertiesClassName(
        string $dataClassName, DataClassParameters $dataClassRetrieveParameters
    ): ParametersHandler
    {
        $dataClassRetrieveParameters->setRetrieveProperties(
            new RetrieveProperties(
                [new PropertiesConditionVariable($dataClassName)]
            )
        );

        return $this;
    }
}