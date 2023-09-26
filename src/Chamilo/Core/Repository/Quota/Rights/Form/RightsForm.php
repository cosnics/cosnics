<?php
namespace Chamilo\Core\Repository\Quota\Rights\Form;

use Chamilo\Core\Repository\Quota\Rights\Rights;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class RightsForm extends FormValidator
{
    const PROPERTY_ACCESS = 'targets';

    private $form_user;

    public function __construct($form_user, $action)
    {
        parent::__construct('rights', 'post', $action);
        $this->form_user = $form_user;
        
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(UserEntity::getInstance()->get_element_finder_type());
        $types->add_element_type(PlatformGroupEntity::getInstance()->get_element_finder_type());
        $this->addElement('advanced_element_finder', self::PROPERTY_ACCESS, null, $types);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'floppy-save');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $default_elements = new AdvancedElementFinderElements();
        $targets_entities = Rights::getInstance()->get_quota_targets_entities();
        $user_entity = UserEntity::getInstance();
        $group_entity = PlatformGroupEntity::getInstance();
        
        foreach ($targets_entities[UserEntity::ENTITY_TYPE] as $entity)
        {
            $default_elements->add_element($user_entity->get_element_finder_element($entity));
        }
        
        foreach ($targets_entities[PlatformGroupEntity::ENTITY_TYPE] as $entity)
        {
            $default_elements->add_element($group_entity->get_element_finder_element($entity));
        }
        
        $this->getElement(self::PROPERTY_ACCESS)->setDefaultValues($default_elements);
        
        parent::setDefaults(array());
    }

    public function set_rights()
    {
        $values = $this->exportValues();
        
        $rights_util = Rights::getInstance();
        $location = $rights_util->get_quota_root();
        
        $targets_entities = Rights::getInstance()->get_quota_targets_entities();
        
        $location_id = $location->get_id();
        
        if (! isset($values[self::PROPERTY_ACCESS][UserEntity::ENTITY_TYPE]))
        {
            $values[self::PROPERTY_ACCESS][UserEntity::ENTITY_TYPE] = array();
        }
        
        if (! isset($values[self::PROPERTY_ACCESS][PlatformGroupEntity::ENTITY_TYPE]))
        {
            $values[self::PROPERTY_ACCESS][PlatformGroupEntity::ENTITY_TYPE] = array();
        }
        
        foreach ($values[self::PROPERTY_ACCESS] as $entity_type => $target_ids)
        {
            $to_delete = array_diff((array) $targets_entities[$entity_type], $target_ids);
            $to_add = array_diff($target_ids, (array) $targets_entities[$entity_type]);
            
            foreach ($to_add as $target_id)
            {
                if (! $rights_util->invert_quota_location_entity_right(
                    Rights::VIEW_RIGHT, 
                    $target_id, 
                    $entity_type, 
                    $location_id))
                {
                    return false;
                }
            }
            
            foreach ($to_delete as $target_id)
            {
                $location_entity_right = Rights::getInstance()->get_quota_location_entity_right(
                    $target_id, 
                    $entity_type);
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class_name(), 
                        RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID), 
                    new StaticConditionVariable($location_entity_right->get_id()));
                
                if (! DataManager::deletes(RightsLocationEntityRightGroup::class_name(), $condition))
                {
                    return false;
                }
                
                if (! $rights_util->invert_quota_location_entity_right(
                    Rights::VIEW_RIGHT, 
                    $target_id, 
                    $entity_type, 
                    $location_id))
                {
                    return false;
                }
            }
        }
        
        return true;
    }
}
