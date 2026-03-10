<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Exception;

use Chamilo\Libraries\Protocol\Authentication\Service\NotAllowedExceptionRenderer;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Exception;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Exception
 */
class NotAllowedException extends Exception implements UserExceptionInterface
{
    public function getUserExceptionRendererClassName(): string
    {
        return NotAllowedExceptionRenderer::class;
    }
}
