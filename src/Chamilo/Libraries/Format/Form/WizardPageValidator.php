<?php
namespace Chamilo\Libraries\Format\Form;

/**
 *
 * @package common.html.formvalidator
 */
// $Id: wizard_page_validator.class.php 128 2009-11-09 13:13:20Z vanpouckesven $

/**
 * Objects of this class can be used to create/manipulate/validate user input.
 */
class WizardPageValidator extends FormValidator
{

    /**
     * Contains the mapping of actions to corresponding HTML_QuickForm_Action objects
     * 
     * @var array
     */
    public $_actions = array();

    /**
     * Contains a reference to a Controller object containing this page
     * 
     * @var HTML_QuickForm_Controller
     * @access public
     */
    public $controller = null;

    /**
     * Should be set to true on first call to buildForm()
     * 
     * @var bool
     */
    public $_formBuilt = false;

    /**
     * Class constructor
     * 
     * @access public
     */
    public function __construct($formName, $method = 'post', $action = '', $target = '', $attributes = null)
    {
        parent::__construct($formName, $method, $action, $target, $attributes);
    }

    /**
     * Registers a handler for a specific action.
     * 
     * @access public
     * @param string name of the action
     * @param HTML_QuickForm_Action the handler for the action
     */
    public function addAction($actionName, &$action)
    {
        $this->_actions[$actionName] = & $action;
    }

    /**
     * Handles an action.
     * If an Action object was not registered here, controller's handle()
     * method will be called.
     * 
     * @access public
     * @param string Name of the action
     * @throws PEAR_Error
     */
    public function handle($actionName)
    {
        if (isset($this->_actions[$actionName]))
        {
            return $this->_actions[$actionName]->perform($this, $actionName);
        }
        else
        {
            return $this->controller->handle($this, $actionName);
        }
    }

    /**
     * Returns a name for a submit button that will invoke a specific action.
     * 
     * @access public
     * @param string Name of the action
     * @return string "name" attribute for a submit button
     */
    public function getButtonName($actionName)
    {
        return '_qf_' . $this->getAttribute('id') . '_' . $actionName;
    }

    /**
     * Loads the submit values from the array.
     * The method is NOT intended for general usage.
     * 
     * @param array 'submit' values
     * @access public
     */
    public function loadValues($values)
    {
        $this->_flagSubmitted = true;
        $this->_submitValues = $values;
        foreach (array_keys($this->_elements) as $key)
        {
            $this->_elements[$key]->onQuickFormEvent('updateValue', null, $this);
        }
    }

    /**
     * Builds a form.
     * You should override this method when you subclass HTML_QuickForm_Page,
     * it should contain all the necessary addElement(), applyFilter(), addRule()
     * and possibly setDefaults() and setConstants() calls. The method will be
     * called on demand, so please be sure to set $_formBuilt property to true to
     * assure that the method works only once.
     * 
     * @access public
     * @abstract
     *
     */
    public function buildForm()
    {
        $this->_formBuilt = true;
    }

    /**
     * Checks whether the form was already built.
     * 
     * @access public
     * @return bool
     */
    public function isFormBuilt()
    {
        return $this->_formBuilt;
    }

    /**
     * Sets the default action invoked on page-form submit
     * This is necessary as the user may just press Enter instead of
     * clicking one of the named submit buttons and then no action name will
     * be passed to the script.
     * 
     * @access public
     * @param string default action name
     */
    public function setDefaultAction($actionName)
    {
        if ($this->elementExists('_qf_default'))
        {
            $element = & $this->getElement('_qf_default');
            $element->setValue($this->getAttribute('id') . ':' . $actionName);
        }
        else
        {
            $this->addElement('hidden', '_qf_default', $this->getAttribute('id') . ':' . $actionName);
        }
    }

    /**
     * Returns 'safe' elements' values
     * 
     * @param mixed Array/string of element names, whose values we want. If not set then return all elements.
     * @param bool Whether to remove internal (_qf_...) values from the resultant array
     */
    public function exportValues($elementList = null, $filterInternal = false)
    {
        $values = parent::exportValues($elementList);
        if ($filterInternal)
        {
            foreach (array_keys($values) as $key)
            {
                if (0 === strpos($key, '_qf_'))
                {
                    unset($values[$key]);
                }
            }
        }
        return $values;
    }
}
