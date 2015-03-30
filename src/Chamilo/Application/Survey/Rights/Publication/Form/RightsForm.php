<?php
namespace Chamilo\Application\Survey\Rights\Publication\Form;

use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class RightsForm extends FormValidator
{
    const PROPERTY_ACCESS = 'targets';

    private $form_user;

    private $right;

    private $publication_id;

    public function __construct($form_user, $action, $right, $publication_id)
    {
        parent :: __construct('rights', 'post', $action);
        $this->form_user = $form_user;
        $this->right = $right;
        $this->publication_id = $publication_id;
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(UserEntity :: get_element_finder_type());
        $types->add_element_type(PlatformGroupEntity :: get_element_finder_type());
        $this->addElement('advanced_element_finder', self :: PROPERTY_ACCESS, null, $types);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive save'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function setDefaults()
    {
        $default_elements = new AdvancedElementFinderElements();
        $targets_entities = Rights :: get_instance()->get_publication_targets_entities(
            $this->right, 
            $this->publication_id);
        $user_entity = UserEntity :: get_instance();
        $group_entity = PlatformGroupEntity :: get_instance();
        
        foreach ($targets_entities[UserEntity :: ENTITY_TYPE] as $entity)
        {
            $default_elements->add_element($user_entity->get_element_finder_element($entity));
        }
        
        foreach ($targets_entities[PlatformGroupEntity :: ENTITY_TYPE] as $entity)
        {
            $default_elements->add_element($group_entity->get_element_finder_element($entity));
        }
        
        $this->getElement(self :: PROPERTY_ACCESS)->setDefaultValues($default_elements);
        
        parent :: setDefaults(array());
    }

    public function set_rights()
    {
        $values = $this->exportValues();
        
        $rights_util = Rights :: get_instance();
        
        $location = $rights_util->get_publication_location($this->publication_id);
        
        $targets_entities = Rights :: get_instance()->get_publication_targets_entities(
            $this->right, 
            $this->publication_id);
        
        $location_id = $location->get_id();
        
        if (! isset($values[self :: PROPERTY_ACCESS][UserEntity :: ENTITY_TYPE]))
        {
            $values[self :: PROPERTY_ACCESS][UserEntity :: ENTITY_TYPE] = array();
        }
        
        if (! isset($values[self :: PROPERTY_ACCESS][PlatformGroupEntity :: ENTITY_TYPE]))
        {
            $values[self :: PROPERTY_ACCESS][PlatformGroupEntity :: ENTITY_TYPE] = array();
        }
        
        foreach ($values[self :: PROPERTY_ACCESS] as $entity_type => $target_ids)
        {
            $to_delete = array_diff((array) $targets_entities[$entity_type], $target_ids);
            $to_add = array_diff($target_ids, (array) $targets_entities[$entity_type]);
            
            foreach ($to_add as $target_id)
            {
                if (! $rights_util->invert_location_entity_right($this->right, $target_id, $entity_type, $location_id))
                {
                    return false;
                }
            }
            
            foreach ($to_delete as $target_id)
            {
                if (! $rights_util->invert_location_entity_right($this->right, $target_id, $entity_type, $location_id))
                {
                    return false;
                }
            }
        }
        
        return true;
    }
}
