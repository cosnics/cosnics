<?php
namespace Chamilo\Libraries\Architecture\Exception;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Security\Service\SecurityUtilities;
use Exception;

/**
 * Extension on the exception class to make clear to the system that this is an exception
 * that should be shown to the user
 *
 * @package Chamilo\Libraries\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserException extends Exception
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Exception
     */
    public function __construct($message)
    {
        parent::__construct($this->getSecurity()->removeXSS($message));
    }

    protected function getSecurity(): SecurityUtilities
    {
        return $this->getService(SecurityUtilities::class);
    }
}