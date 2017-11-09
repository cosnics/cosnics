<?php
namespace Chamilo\Core\Rights\Editor\Form;

use Chamilo\Core\Rights\RightsLocation;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form to display the rights on a more usable way with radio buttons.
 * 
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 */
class ManageForm extends FormValidator
{
    const PROPERTY_INHERIT = 'inherit';
    const PROPERTY_RIGHT_OPTION = 'right_option';
    const PROPERTY_SUBMIT = 'submit';
    const PROPERTY_RESET = 'reset';
    const PROPERTY_BUTTONS = 'buttons';
    const PROPERTY_TARGETS = 'targets';
    const PROPERTY_GROUP_USE = 'group_use';
    const PROPERTY_ACTION = 'action';
    const PROPERTY_RIGHT = 'right';
    const ACTION_GRANT = 1;
    const ACTION_DENY = 2;
    const INHERIT_TRUE = 0;
    const INHERIT_FALSE = 1;
    const RIGHT_OPTION_ALL = 0;
    const RIGHT_OPTION_ME = 1;
    const RIGHT_OPTION_SELECT = 2;

    /**
     * The context for the rights form
     */
    private $context;

    /**
     * The selected location ids
     * 
     * @var Array<Int>
     */
    private $locations;

    /**
     * The available rights
     * 
     * @var Array<Int>
     */
    private $available_rights;

    /**
     * The selected entities
     * 
     * @param Array<RightEntity>
     */
    private $entities;

    public function __construct($action, $context, $locations, $available_rights, $entities)
    {
        parent::__construct('manager', 'post', $action);
        
        $this->context = $context;
        $this->locations = $locations;
        $this->entities = $entities;
        $this->available_rights = $available_rights;
        
        $this->build_form();
    }

    /**
     * Builds the form
     */
    public function build_form()
    {
        $this->build_inheritance_form();
        $this->addElement('html', '<div style="display:none;" class="specific_rights_selector_box">');
        
        $this->build_right_form();
        
        $this->addElement('html', '</div>');
        
        $this->build_form_footer();
    }

    /**
     * Builds the inheritance form (wheter to inherit the rights from parent location or not)
     */
    private function build_inheritance_form()
    {
        $has_root_location = false;
        foreach ($this->locations as $location)
        {
            if ($location->get_parent_id() == 0) // root location
            {
                $has_root_location = true;
            }
        }
        $this->addElement('category', Translation::get('Inheritance'));
        
        $group = array();
        
        if (! $has_root_location)
        {
            $group[] = & $this->createElement(
                'radio', 
                null, 
                null, 
                Translation::get('InheritRights'), 
                self::INHERIT_TRUE, 
                array('class' => 'inherit_rights_selector'));
        }
        else
        {
            $group[] = & $this->createElement(
                'radio', 
                null, 
                null, 
                Translation::get('InheritRights'), 
                self::INHERIT_TRUE, 
                array('class' => 'inherit_rights_selector', 'disabled' => 'disabled'));
        }
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('UseSpecificRights'), 
            self::INHERIT_FALSE, 
            array('class' => 'specific_rights_selector'));
        
        $this->addGroup($group, self::PROPERTY_INHERIT, null, '');
        
        $this->addElement('category');
    }

    /**
     * Builds the form for a given right
     * 
     * @param String $right_name
     * @param int $right_id
     */
    private function build_right_form()
    {
        $this->addElement('category', ' ');
        
        $element_template = array();
        $element_template[] = '<div class="column">';
        $element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        
        $this->addElement(
            'select', 
            self::PROPERTY_ACTION, 
            '', 
            array(self::ACTION_GRANT => Translation::get('Grant'), self::ACTION_DENY => Translation::get('Deny')));
        
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_ACTION);
        
        $this->addElement('select', self::PROPERTY_RIGHT, '', array_flip($this->available_rights), 'multiple');
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_RIGHT);
        
        $group = array();
        
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('Everyone'), 
            self::RIGHT_OPTION_ALL, 
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('OnlyForMe'), 
            self::RIGHT_OPTION_ME, 
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('SelectSpecificEntities'), 
            self::RIGHT_OPTION_SELECT, 
            array('class' => 'entity_option_selected'));
        
        $this->addGroup($group, self::PROPERTY_RIGHT_OPTION, '', '');
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_RIGHT_OPTION);
        
        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();
        
        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }
        $this->addElement('category');
        $this->addElement('category', ' ', 'entity_selector_box');
        $element_template = '{element}';
        
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS, null, $types);
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_TARGETS);
        $this->addElement('category');
    }

    /**
     * Builds the form footer
     */
    private function build_form_footer()
    {
        $buttons = array();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            self::PROPERTY_SUBMIT, 
            Translation::get('Submit', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            self::PROPERTY_RESET, 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, self::PROPERTY_BUTTONS, null, '&nbsp;', false);
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Rights\Editor', true) . 'RightsForm.js'));
    }

    /**
     * Handles the click on the form submit
     */
    public function handle_form_submit()
    {
        $values = $this->exportValues();
        $succes = true;
        
        foreach ($this->locations as $location)
        {
            if ($values[self::PROPERTY_INHERIT] == self::INHERIT_TRUE)
            {
                if (! $location->inherits())
                {
                    $location->inherit();
                    $succes &= $location->update();
                }
            }
            else
            {
                if ($location->inherits())
                {
                    $location->disinherit();
                    $succes &= $location->update();
                }
                
                $succes &= $this->handle_rights($location);
            }
        }
        
        return $succes;
    }

    /**
     * Handles the rights options for the specific location
     * 
     * @param RightsLocation $location
     */
    private function handle_rights($location)
    {
        $values = $this->exportValues();
        $rights_util = RightsUtil::getInstance();
        
        $location_id = $location->get_id();
        
        $succes = true;
        
        $option = $values[self::PROPERTY_RIGHT_OPTION];
        $action = $values[self::PROPERTY_ACTION];
        foreach ($values[self::PROPERTY_RIGHT] as $right_id)
        {
            switch ($option)
            {
                case self::RIGHT_OPTION_ALL :
                    if ($action == self::ACTION_GRANT)
                    {
                        $succes &= $rights_util->set_location_entity_right(
                            $this->context, 
                            $right_id, 
                            0, 
                            0, 
                            $location_id);
                    }
                    else
                    {
                        $succes &= $rights_util->unset_location_entity_right(
                            $this->context, 
                            $right_id, 
                            0, 
                            0, 
                            $location_id);
                    }
                    break;
                case self::RIGHT_OPTION_ME :
                    if ($action == self::ACTION_GRANT)
                    {
                        $succes &= $rights_util->set_location_entity_right(
                            $this->context, 
                            $right_id, 
                            Session::get_user_id(), 
                            1, 
                            $location_id);
                    }
                    else
                    {
                        $succes &= $rights_util->unset_location_entity_right(
                            $this->context, 
                            $right_id, 
                            Session::get_user_id(), 
                            1, 
                            $location_id);
                    }
                    break;
                case self::RIGHT_OPTION_SELECT :
                    foreach ($values[self::PROPERTY_TARGETS] as $entity_type => $target_ids)
                    {
                        foreach ($target_ids as $target_id)
                        {
                            if ($action == self::ACTION_GRANT)
                            {
                                $succes &= $rights_util->set_location_entity_right(
                                    $this->context, 
                                    $right_id, 
                                    $target_id, 
                                    $entity_type, 
                                    $location_id);
                            }
                            
                            else
                            
                            {
                                $succes &= $rights_util->unset_location_entity_right(
                                    $this->context, 
                                    $right_id, 
                                    $target_id, 
                                    $entity_type, 
                                    $location_id);
                            }
                        }
                    }
            }
        }
        
        return $succes;
    }
}
