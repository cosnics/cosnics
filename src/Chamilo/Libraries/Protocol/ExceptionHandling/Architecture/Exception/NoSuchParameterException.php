<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\NoSuchParameterExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchParameterException extends Exception implements UserExceptionInterface
{
    protected string $parameter;

    public function __construct(
        string $parameter, ?string $message = null, int $code = 0, ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->parameter = $parameter;
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter): NoSuchParameterException
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchParameterExceptionRenderer::class;
    }
}
