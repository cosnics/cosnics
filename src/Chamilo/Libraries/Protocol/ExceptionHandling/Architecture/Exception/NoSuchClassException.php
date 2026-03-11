<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\NoSuchClassExceptionRenderer;
use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchClassException extends Exception implements UserExceptionInterface
{
    protected string $implementationType;

    protected string $type;

    public function __construct(
        string $implementationType, string $type, ?string $message = null, int $code = 0,
        ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->implementationType = $implementationType;
        $this->type = $type;
    }

    public function getImplementationType(): string
    {
        return $this->implementationType;
    }

    public function setImplementationType(string $implementationType): NoSuchClassException
    {
        $this->implementationType = $implementationType;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): NoSuchClassException
    {
        $this->type = $type;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchClassExceptionRenderer::class;
    }
}
