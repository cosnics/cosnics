<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception;

use Chamilo\Core\User\Storage\DataClass\User;
use Exception;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365UserNotExistsException extends Exception
{
    /**
     * Office365UserNotExistsException constructor.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function __construct(User $user)
    {
        parent::__construct('The system could not find a valid office365 user for given user ' . $user->get_fullname());
    }
}