<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Security;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 */
class ObjectNotExistException extends UserException
{

    public function __construct($object_translation, $id = null)
    {
        // Make sure that the ID is safe for printing
        $security = new Security();
        $id = $security->remove_XSS($id, false);
        
        parent::__construct(
            Translation::get('ObjectNotExist', array('OBJECT' => $object_translation, 'OBJECT_ID' => $id)));
    }
}
