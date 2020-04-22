<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Exception;

use Chamilo\Core\User\Storage\DataClass\User;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AzureUserNotExistsException extends Exception
{

    /**
     * AzureUserNotExistsException constructor.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function __construct(User $user)
    {
        parent::__construct(
            'The system could not find a valid Azure Active Directory user for given user ' . $user->get_fullname()
        );
    }
}