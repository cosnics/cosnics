<?php
namespace Chamilo\Core\Install\Architecture\Domain;

/**
 * Class that holds the result from a package installation.
 * Keeps track of the success status, the installation messages
 * and the context of the installer
 *
 * @author  Phillipe
 * @author  Sven Vanpoucke - Hogeschool Gent - added context
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StepResult
{

    protected string $context;

    /**
     * @var string[] Messages produced during installation step
     */
    protected array $messages;

    protected bool $success;

    public function __construct(bool $success = false, ?array $messages = null, ?string $context = null)
    {
        if (is_null($messages))
        {
            $messages = [];
        }

        if (!is_array($messages))
        {
            $messages = [$messages];
        }

        $this->success = $success;
        $this->messages = $messages;
        $this->context = $context;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
