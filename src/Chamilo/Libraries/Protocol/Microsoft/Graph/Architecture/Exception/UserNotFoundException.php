<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Chamilo\Core\User\Storage\DataClass\User;
use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserNotFoundException extends Exception
{
    public function __construct(User $user)
    {
        parent::__construct(
            'The system could not find a valid Azure Active Directory user for given user ' . $user->getFullName()
        );
    }
}