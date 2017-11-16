<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataActions
{
    /**
     * @var \Chamilo\Libraries\Format\DataAction\ActionInterface[]
     */
    protected $actions;

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param ActionInterface[] $actions
     *
     * @return \Chamilo\Libraries\Format\DataAction\DataActions
     */
    public function setActions(array $actions)
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