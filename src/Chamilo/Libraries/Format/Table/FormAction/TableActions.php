<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

/**
 * This class represents a container for the table form actions
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\FormAction
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableActions
{

    /**
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableAction[]
     */
    private array $actions;

    private string $identifierName;

    private string $tableNamespace;

    /**
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableAction[] $actions
     */
    public function __construct(string $tableNamespace, string $identifierName, array $actions = [])
    {
        $this->actions = $actions;
        $this->identifierName = $identifierName;
        $this->tableNamespace = $tableNamespace;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableAction $formAction
     */
    public function addAction(TableAction $formAction)
    {
        $this->actions[] = $formAction;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableAction[] $form_actions
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getIdentifierName(): string
    {
        return $this->identifierName;
    }

    public function getNamespace(): string
    {
        return $this->tableNamespace;
    }

    public function hasActions(): bool
    {
        return count($this->actions) >= 1;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableAction[] $actions
     */
    public function seActions(array $actions)
    {
        $this->actions = $actions;
    }

    public function setIdentifierName(string $identifierName)
    {
        $this->identifierName = $identifierName;
    }

    public function setNamespace(string $namespace)
    {
        $this->tableNamespace = $namespace;
    }
}
