<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Service\NoSuchObjectExceptionRenderer;
use Exception;

/**
 * This class represents an object not exists exception.
 * Throw this if you retrieved an object from the request
 * parameter that is not valid
 *
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 */
class NoSuchObjectException extends Exception implements UserExceptionInterface
{
    protected string $objectIdentifier;

    protected string $objectType;

    public function __construct(
        string $objectType, string $objectIdentifier, ?string $message = null, int $code = 0,
        ?Exception $previousException = null
    )
    {
        parent::__construct($message, $code, $previousException);

        $this->objectType = $objectType;
        $this->objectIdentifier = $objectIdentifier;
    }

    public function getObjectIdentifier(): string
    {
        return $this->objectIdentifier;
    }

    public function setObjectIdentifier(string $objectIdentifier): NoSuchObjectException
    {
        $this->objectIdentifier = $objectIdentifier;

        return $this;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function setObjectType(string $objectType): NoSuchObjectException
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchObjectExceptionRenderer::class;
    }
}
