<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class ParameterNotDefinedException extends UserException
{

    /**
     *
     * @param string $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct(Translation::get('ParameterNotDefined', array('PARAMETER' => $parameter)));
    }
}
