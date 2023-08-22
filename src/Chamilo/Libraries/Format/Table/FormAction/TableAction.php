<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

/**
 * This class represents a table form action
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
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

    public function getConfirm(): bool
    {
        return $this->confirm;
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setAction(string $action)
    {
        $this->action = $action;
    }

    public function setConfirm(bool $confirm)
    {
        $this->confirm = $confirm;
    }

    public function setConfirmationMessage(?string $confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
