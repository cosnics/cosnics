<?php
namespace Chamilo\Core\User\Architecture\Exception;

use Exception;

/**
 * @package Chamilo\Core\User\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserAlreadyExistsException extends Exception
{
    public function __construct(protected ?string $username)
    {
        parent::__construct('The given username (' . $this->username . ') is already taken');
    }
}