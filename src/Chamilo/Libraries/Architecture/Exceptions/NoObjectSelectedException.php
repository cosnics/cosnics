<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class NoObjectSelectedException extends UserException
{

    public function __construct(string $objectTranslation)
    {
        parent::__construct(
            $this->getTranslator()->trans('NoObjectSelected', ['OBJECT' => $objectTranslation],
                StringUtilities::LIBRARIES)
        );
    }
}
