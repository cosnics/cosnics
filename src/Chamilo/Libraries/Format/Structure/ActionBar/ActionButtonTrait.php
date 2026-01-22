<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait ActionButtonTrait
{

    private ?string $action;

    private ?string $confirmationMessage;

    private ?string $target;

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function setConfirmationMessage(?string $confirmationMessage): static
    {
        $this->confirmationMessage = $confirmationMessage;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Initialize method as replacement for constructor due to PHP issue
     * https://bugs.php.net/bug.php?id=65576
     * TODO: fix this once everyone moves to PHP 5.6
     */
    public function initializeActionButton(
        ?string $action = null, ?string $confirmationMessage = null, ?string $target = null
    ): static
    {
        $this->setAction($action);
        $this->setConfirmationMessage($confirmationMessage);
        $this->setTarget($target);

        return $this;
    }

    public function needsConfirmation(): bool
    {
        return !is_null($this->getConfirmationMessage());
    }
}
