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
    private $form_actions;

    /**
     * The namespace of the table
     * 
     * @var string
     */
    private $namespace;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param string $action
     * @param TableFormAction[] $form_actions
     * @param string $namespace
     */
    public function __construct($namespace, $form_actions = array())
    {
        $this->form_actions = $form_actions;
        $this->namespace = $namespace;
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
        return $this->namespace;
    }

    /**
     * Sets the namespace of the table
     * 
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the form actions
     * 
     * @return TableFormAction[] $form_actions
     */
    public function get_form_actions()
    {
        return $this->form_actions;
    }

    /**
     * Sets the form actions
     * 
     * @param TableFormAction[] $form_actions
     */
    public function set_form_actions($form_actions)
    {
        $this->form_actions = $form_actions;
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
        $this->form_actions[] = $form_action;
    }

    /**
     * Returns whether or not this container has form actions
     * 
     * @return bool
     */
    public function has_form_actions()
    {
        return count($this->form_actions) >= 1;
    }
}
