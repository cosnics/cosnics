<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

/**
 * This class represents a container for the table form actions
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TableFormActions
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The form actions
     * 
     * @var TableFormAction[]
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
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param string $tableNamespace
     * @param TableFormAction[] $formActions
     * @param string $namespace
     */
    public function __construct($tableNamespace, $identifierName, $formActions = array())
    {
        $this->formActions = $formActions;
        $this->identifierName = $identifierName;
        $this->tableNamespace = $tableNamespace;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
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
     * Sets the namespace of the table
     * 
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->tableNamespace = $namespace;
    }

    /**
     * Returns the form actions
     * 
     * @return TableFormAction[] $form_actions
     */
    public function get_form_actions()
    {
        return $this->formActions;
    }

    /**
     * Sets the form actions
     * 
     * @param TableFormAction[] $form_actions
     */
    public function set_form_actions($form_actions)
    {
        $this->formActions = $form_actions;
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds a form action
     * 
     * @param TableFormAction $form_action
     */
    public function add_form_action($form_action)
    {
        $this->formActions[] = $form_action;
    }

    /**
     * Returns whether or not this container has form actions
     * 
     * @return bool
     */
    public function has_form_actions()
    {
        return count($this->formActions) >= 1;
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
}
