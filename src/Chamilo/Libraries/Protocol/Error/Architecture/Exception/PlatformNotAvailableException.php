<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Exception;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Service\PlatformNotAvailableExceptionRenderer;
use Exception;

/**
 * Throws this exception when the platform is not available
 *
 * @package Chamilo\Libraries\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformNotAvailableException extends Exception implements UserExceptionInterface
{
    public function getUserExceptionRendererClassName(): string
    {
        return PlatformNotAvailableExceptionRenderer::class;
    }
}
