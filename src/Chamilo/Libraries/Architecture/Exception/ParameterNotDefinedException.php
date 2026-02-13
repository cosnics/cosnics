<?php
namespace Chamilo\Libraries\Architecture\Exception;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exception
 */
class ParameterNotDefinedException extends UserException
{
    public function __construct(string $parameter)
    {
        parent::__construct($this->getTranslator()->trans('ParameterNotDefined', ['%Parameter%' => $parameter]));
    }
}
