<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class ParameterNotDefinedException extends UserException
{

    public function __construct(string $parameter)
    {
        $this->initializeContainer();
        parent::__construct($this->getTranslator()->trans('ParameterNotDefined', ['PARAMETER' => $parameter]));
    }
}
