<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction;

/**
 * @package Chamilo\Libraries\Format\Table\FormAction
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableAction
{

    private string $action;

    private bool $confirm;

    private ?string $confirmationMessage;

    private string $title;

    public function __construct(string $action, string $title, bool $confirm = true, ?string $confirmationMessage = null
    )
    {
        $this->action = $action;
        $this->title = $title;
        $this->confirm = $confirm;
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getConfirm(): bool
    {
        return $this->confirm;
    }

    public function setConfirm(bool $confirm): static
    {
        $this->confirm = $confirm;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
