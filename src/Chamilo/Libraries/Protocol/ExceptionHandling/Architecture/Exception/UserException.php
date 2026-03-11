<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\UserExceptionRenderer;
use Exception;

/**
 * Extension on the exception class to make clear to the system that this is an exception
 * that should be shown to the user
 *
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserException extends Exception implements UserExceptionInterface
{
    protected ?string $title;

    public function __construct(
        ?string $message = null, ?string $title = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): UserException
    {
        $this->title = $title;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return UserExceptionRenderer::class;
    }
}