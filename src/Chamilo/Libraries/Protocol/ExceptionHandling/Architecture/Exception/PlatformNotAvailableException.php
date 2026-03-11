<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\PlatformNotAvailableExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PlatformNotAvailableException extends Exception implements UserExceptionInterface
{
    public function getUserExceptionRendererClassName(): string
    {
        return PlatformNotAvailableExceptionRenderer::class;
    }
}
