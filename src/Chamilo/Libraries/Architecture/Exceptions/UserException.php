<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Platform\Security;
use Exception;

/**
 * Extension on the exception class to make clear to the system that this is an exception
 * that should be shown to the user
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserException extends Exception
{
    use DependencyInjectionContainerTrait;

    public function __construct($message)
    {
        $this->initializeContainer();
        parent::__construct($this->getSecurity()->removeXSS($message));
    }

    /**
     * @return \Chamilo\Libraries\Platform\Security
     */
    protected function getSecurity(): Security
    {
        return $this->getService(Security::class);
    }
}