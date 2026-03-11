<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Storage\Service\DisplayOrderExceptionRenderer;
use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderException extends Exception implements UserExceptionInterface
{
    protected string $className;

    protected ?int $displayOrder;

    protected string $displayOrderContext;

    protected string $identifier;

    protected int $numberOfOtherDisplayOrdersInContext;

    public function __construct(
        string $className, string $identifier, string $displayOrderContext, ?int $displayOrder,
        int $numberOfOtherDisplayOrdersInContext, string $message = '', int $code = 0, ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->className = $className;
        $this->identifier = $identifier;
        $this->displayOrderContext = $displayOrderContext;
        $this->displayOrder = $displayOrder;
        $this->numberOfOtherDisplayOrdersInContext = $numberOfOtherDisplayOrdersInContext;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function getDisplayOrderContext(): string
    {
        return $this->displayOrderContext;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getNumberOfOtherDisplayOrdersInContext(): int
    {
        return $this->numberOfOtherDisplayOrdersInContext;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return DisplayOrderExceptionRenderer::class;
    }
}