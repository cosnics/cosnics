<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Translation\Translation;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class NoObjectSelectedException extends UserException
{

    /**
     *
     * @param string $objectTranslation
     */
    public function __construct($objectTranslation)
    {
        parent::__construct(Translation::get('NoObjectSelected', array('OBJECT' => $objectTranslation)));
    }
}
