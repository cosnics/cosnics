<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CompositeDataClassTypeException extends Exception
{
    private string $dataClassName;

    private RecordRetrieveParameters $parameters;

    public function __construct(
        string $dataClassName, RecordRetrieveParameters $parameters
    )
    {
        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;

        parent::__construct(
            'A composite DataClass type could not be determined for the DataClass ' . $dataClassName .
            ' with the following parameters: ' . serialize($parameters)
        );
    }

    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function getParameters(): RecordRetrieveParameters
    {
        return $this->parameters;
    }

    public function setDataClassName(string $dataClassName): CompositeDataClassTypeException
    {
        $this->dataClassName = $dataClassName;

        return $this;
    }

    public function setParameters(RecordRetrieveParameters $parameters): CompositeDataClassTypeException
    {
        $this->parameters = $parameters;

        return $this;
    }

}