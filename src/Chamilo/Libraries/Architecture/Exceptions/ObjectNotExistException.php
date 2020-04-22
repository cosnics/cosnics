<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class ObjectNotExistException extends UserException
{

    /**
     *
     * @param string $objectTranslation
     * @param integer $id
     */
    public function __construct($objectTranslation, $id = null)
    {
        // Make sure that the ID is safe for printing
        $security = new Security();
        $id = $security->removeXSS($id, false);

        parent::__construct(
            Translation::get('ObjectNotExist', array('OBJECT' => $objectTranslation, 'OBJECT_ID' => $id))
        );
    }
}
