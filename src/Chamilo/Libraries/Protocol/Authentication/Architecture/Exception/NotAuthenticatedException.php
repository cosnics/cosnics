<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Exception;

use Chamilo\Libraries\Protocol\Authentication\Service\NotAuthenticatedExceptionRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Exception;

/**
 * Exception to be thrown when the user is not authenticated
 *
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotAuthenticatedException extends Exception implements UserExceptionInterface
{
    public function getUserExceptionRendererClassName(): string
    {
        return NotAuthenticatedExceptionRenderer::class;
    }
}