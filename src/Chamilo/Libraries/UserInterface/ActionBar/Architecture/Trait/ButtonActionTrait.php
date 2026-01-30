<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonActionTrait
{
    private ?string $action = null;

    private ?string $confirmationMessage = null;

    private ?string $target = null;

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

    public function setConfirmationMessage(?string $message): static
    {
        $this->confirmationMessage = $message;

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

    public function needsConfirmation(): bool
    {
        return !is_null($this->getConfirmationMessage());
    }
}
