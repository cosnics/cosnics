<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

/**
 * This class represents a container for the table form actions
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\FormAction
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TableFormActions
{

    /**
     * The form actions
     *
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableFormAction[]
     */
    private $formActions;

    /**
     *
     * @var string
     */
    private $identifierName;

    /**
     * The namespace of the table
     *
     * @var string
     */
    private $tableNamespace;

    /**
     * Constructor
     *
     * @param string $tableNamespace
     * @param string $identifierName
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormAction[] $formActions
     */
    public function __construct($tableNamespace, $identifierName, $formActions = array())
    {
        $this->formActions = $formActions;
        $this->identifierName = $identifierName;
        $this->tableNamespace = $tableNamespace;
    }

    /**
     * Adds a form action
     *
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormAction $formAction
     */
    public function add_form_action($formAction)
    {
        $this->formActions[] = $formAction;
    }

    /**
     *
     * @return string
     */
    public function getIdentifierName()
    {
        return $this->identifierName;
    }

    /**
     *
     * @param string $identifierName
     */
    public function setIdentifierName($identifierName)
    {
        $this->identifierName = $identifierName;
    }

    /**
     * Returns the form actions
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormAction[] $form_actions
     */
    public function get_form_actions()
    {
        return $this->formActions;
    }

    /**
     * Sets the form actions
     *
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormAction[] $formActions
     */
    public function set_form_actions($formActions)
    {
        $this->formActions = $formActions;
    }

    /**
     * Returns the namespace of the table
     *
     * @return string
     */
    public function get_namespace()
    {
        return $this->tableNamespace;
    }

    /**
     * Returns whether or not this container has form actions
     *
     * @return boolean
     */
    public function has_form_actions()
    {
        return count($this->formActions) >= 1;
    }

    /**
     * Sets the namespace of the table
     *
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->tableNamespace = $namespace;
    }
}
