<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 */
class NoObjectSelectedException extends UserException
{

    public function __construct($object_translation)
    {
        parent::__construct(Translation::get('NoObjectSelected', array('OBJECT' => $object_translation)));
    }
}
