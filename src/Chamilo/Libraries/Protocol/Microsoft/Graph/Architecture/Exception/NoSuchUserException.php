<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchUserExceptionRenderer;
use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchUserException extends Exception implements UserExceptionInterface
{
    protected User $user;

    public function __construct(
        User $user, string $message = '', int $code = 0, ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchUserExceptionRenderer::class;
    }
}