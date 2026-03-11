<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchCalendarExceptionRenderer;
use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchCalendarException extends Exception implements UserExceptionInterface
{
    protected string $calendarIdentifier;

    protected string $userIdentifier;

    public function __construct(
        string $userIdentifier, string $calendarIdentifier, string $message = '', int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->userIdentifier = $userIdentifier;
        $this->calendarIdentifier = $calendarIdentifier;
    }

    public function getCalendarIdentifier(): string
    {
        return $this->calendarIdentifier;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchCalendarExceptionRenderer::class;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}