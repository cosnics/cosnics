<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchGroupExceptionRenderer;
use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchGroupException extends Exception implements UserExceptionInterface
{
    protected string $groupIdentifier;

    public function __construct(
        string $groupIdentifier, string $message = '', int $code = 0, ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->groupIdentifier = $groupIdentifier;
    }

    public function getGroupIdentifier(): string
    {
        return $this->groupIdentifier;
    }

    public function getUserExceptionRendererClassName(): string
    {
        return NoSuchGroupExceptionRenderer::class;
    }
}