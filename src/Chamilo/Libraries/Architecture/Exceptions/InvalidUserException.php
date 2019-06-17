<?php

namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This exception is thrown when the system detects that the user has been modified throughout the process.
 * (for example when you are viewing a part of the application as another user). This exception should be used in
 * places that are data sensitive (like the repository or the repoviewer) and where the user should not be allowed
 * to be viewing another user
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidUserException extends \Chamilo\Libraries\Architecture\Exceptions\UserException
{

    /**
     * InvalidUserException constructor.
     */
    public function __construct()
    {
        parent::__construct(Translation::get('InvalidUserExceptionMessage', null, Utilities::COMMON_LIBRARIES));
    }
}