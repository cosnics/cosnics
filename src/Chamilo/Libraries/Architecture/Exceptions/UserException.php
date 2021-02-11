<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Security;

/**
 * Extension on the exception class to make clear to the system that this is an exception
 * that should be shown to the user
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserException extends \Exception
{
    /**
     * UserException constructor.
     *
     * @param $message
     */
    public function __construct($message)
    {
        $security = new Security();
        $message = htmlentities($security->removeXSS($message));

        parent::__construct($message);
    }
}
