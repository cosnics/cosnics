<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * Describes a group of actions
 *
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
class ActionGroup extends AbstractNamedAction
{
    const TYPE_NAME = 'group';

    /**
     * @var ActionInterface[]
     */
    protected $actions;

    /**
     * ActionGroup constructor.
     *
     * @param string $name
     * @param string $title
     * @param string $fontAwesomeIconClass
     * @param ActionInterface[] $actions
     */
    public function __construct($name, $title, $fontAwesomeIconClass = null, array $actions = array())
    {
        parent::__construct($name, $title, $fontAwesomeIconClass);

        $this->actions = $actions;
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
     * @param array $baseArray
     *
     * @return array
     */
    public function toArray($baseArray = [])
    {
        $actionsArray = [];

        foreach($this->actions as $action)
        {
            $actionsArray[] = $action->toArray();
        }

        $baseArray['actions'] = $actionsArray;

        return parent::toArray($baseArray);
    }
}