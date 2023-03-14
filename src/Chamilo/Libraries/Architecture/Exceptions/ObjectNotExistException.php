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
class ObjectNotExistException extends UserException
{

    public function __construct(string $objectTranslation, ?string $id = null)
    {
        $this->initializeContainer();
        parent::__construct(
            $this->getTranslator()->trans('ObjectNotExist', array('OBJECT' => $objectTranslation, 'OBJECT_ID' => $id),
                StringUtilities::LIBRARIES)
        );
    }
}
