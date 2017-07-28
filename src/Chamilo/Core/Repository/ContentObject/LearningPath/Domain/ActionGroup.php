<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

/**
 * Describes a group of actions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActionGroup implements ActionInterface
{
    /**
     * A name for the action, used as identifier
     *
     * @var string
     */
    protected $name;

    /**
     * @var ActionInterface[]
     */
    protected $actions;

    /**
     * ActionGroup constructor.
     *
     * @param ActionInterface[] $actions
     */
    public function __construct($name, array $actions = array())
    {
        $this->actions = $actions;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ActionGroup
     */
    public function setName(string $name): ActionGroup
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param ActionInterface[] $actions
     *
     * @return ActionGroup
     */
    public function setActions(array $actions): ActionGroup
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Adds an action to the list of actions
     *
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Converts this object's properties to an array
     *
     * @return array
     */
    public function toArray()
    {
        $actionsArray = [];

        foreach($this->actions as $action)
        {
            $actionsArray[] = $action->toArray();
        }

        return $actionsArray;
    }
}