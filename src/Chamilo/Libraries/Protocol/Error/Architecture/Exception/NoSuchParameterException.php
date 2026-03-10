<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Exception;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Service\NoSuchParameterExceptionRenderer;
use Exception;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exception
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
