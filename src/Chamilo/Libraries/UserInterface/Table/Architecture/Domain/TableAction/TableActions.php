<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableActions
{
    /**
     * @var \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction[]
     */
    private array $actions;

    private string $identifierName;

    private string $tableNamespace;

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction[] $actions
     */
    public function __construct(string $tableNamespace, string $identifierName, array $actions = [])
    {
        $this->actions = $actions;
        $this->identifierName = $identifierName;
        $this->tableNamespace = $tableNamespace;
    }

    public function addAction(TableAction $formAction): static
    {
        $this->actions[] = $formAction;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getIdentifierName(): string
    {
        return $this->identifierName;
    }

    public function setIdentifierName(string $identifierName): static
    {
        $this->identifierName = $identifierName;

        return $this;
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
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction[] $actions
     */
    public function seActions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function setNamespace(string $namespace): static
    {
        $this->tableNamespace = $namespace;

        return $this;
    }
}
