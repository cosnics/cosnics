<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassNoResultException extends Exception
{

    private string $dataClassName;

    private DataClassParameters $parameters;

    public function __construct(
        string $dataClassName, DataClassParameters $parameters, ?string $message = null, ?int $code = null,
        ?Exception $previous = null
    )
    {
        $message = 'No result was found for ' . $dataClassName . '. Additional information: ' . $message;

        parent::__construct($message, $code, $previous);

        $this->dataClassName = $dataClassName;
        $this->parameters = $parameters;
    }

    public function get_class_name(): string
    {
        return $this->dataClassName;
    }

    public function get_parameters(): DataClassParameters
    {
        return $this->parameters;
    }

    public function set_parameters(DataClassParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    public function set_class_name(string $dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }
}
