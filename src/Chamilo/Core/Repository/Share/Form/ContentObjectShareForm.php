<?php
namespace Chamilo\Core\Repository\Share\Form;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: user_view_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.forms
 * @author Sven Vanpoucke
 */
class ContentObjectShareForm extends FormValidator
{
    const PARAM_RIGHT = 'right';
    const PARAM_TARGET = 'targets';
    const PARAM_USER = 'user';
    const PARAM_GROUP = 'group';
    const PARAM_COPY = 'copy';
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $content_object_ids;

    private $form_type;

    private $user;

    /**
     * The entities to edit the rights
     * 
     * @var Array
     */
    private $entities;

    public function __construct($form_type, $content_object_ids, $user, $action)
    {
        parent :: __construct('content_object_share_form', 'post', $action);
        
        $this->content_object_ids = $content_object_ids;
        $this->form_type = $form_type;
        $this->user = $user;
        
        $this->entities = array();
        $this->entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
        $this->entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function get_content_object_ids()
    {
        return $this->content_object_ids;
    }

    public static function factory($form_type, $content_object_ids = array(), $user, $action)
    {
        return new ContentObjectShareForm($form_type, $content_object_ids, $user, $action);
    }

    public function build_basic_form()
    {
        $rights = RepositoryRights :: get_share_rights();
        // $rights[-1] = Translation :: get('--SelectShareRight--', null, Utilities :: COMMON_LIBRARIES);
        // ksort($rights);
        
        $this->addElement(
            'select', 
            self :: PARAM_RIGHT, 
            Translation :: get('Rights', null, \Chamilo\Core\Rights\Manager :: context()), 
            $rights);
        $this->addElement(
            'checkbox', 
            self :: PARAM_COPY, 
            Translation :: get('CopyRight', null, \Chamilo\Core\Rights\Manager :: context()));
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $types = new AdvancedElementFinderElementTypes(array());
        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }
        
        $this->addElement('advanced_element_finder', self :: PARAM_TARGET, Translation :: get('Targets'), $types);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function create_content_object_share()
    {
        $values = $this->exportValues();
        
        $targets = $values[self :: PARAM_TARGET];
        
        return $this->set_rights_for_targets($targets);
    }

    /**
     * Sets the rights for the given targets
     * 
     * @param $targets
     * @return bool
     */
    protected function set_rights_for_targets($targets)
    {
        $values = $this->exportValues();
        
        $right_id = $values[self :: PARAM_RIGHT];
        $copy_right = $values[self :: PARAM_COPY];
        
        $success = true;
        
        $repository_rights = RepositoryRights :: get_instance();
        
        $right_ids = array();
        
        if ($right_id > - 1)
        {
            $right_ids[] = $right_id;
        }
        
        if ($copy_right)
        {
            $right_ids[] = RepositoryRights :: COPY_RIGHT;
        }
        
        foreach ($this->content_object_ids as $content_object_id)
        {
            $location = Manager :: get_current_user_tree_location(Session :: get_user_id(), $content_object_id);
            
            foreach ($targets as $entity_type => $entity_ids)
            {
                foreach ($entity_ids as $entity_id)
                {
                    if (! $repository_rights->clear_share_entity_rights($location, $entity_type, $entity_id))
                    {
                        $success = false;
                    }
                    
                    foreach ($right_ids as $right_id)
                    {
                        if (! $repository_rights->invert_repository_location_entity_right(
                            $right_id, 
                            $entity_id, 
                            $entity_type, 
                            $location->get_id()))
                        {
                            $success = false;
                        }
                    }
                }
            }
        }
        
        return $success;
    }

    public function update_content_object_share($target_user_ids = array(), $target_group_ids = array())
    {
        $targets = array();
        
        $targets[UserEntity :: ENTITY_TYPE] = $target_user_ids;
        $targets[PlatformGroupEntity :: ENTITY_TYPE] = $target_group_ids;
        
        return $this->set_rights_for_targets($targets);
    }

    /**
     * Sets default values.
     * 
     * @param int[] $target_user_ids
     * @param int[] $target_group_ids
     */
    public function set_default_rights($target_user_ids = array(), $target_group_ids = array())
    {
        $location = RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT, 
            $this->content_object_ids[0], 
            Session :: get_user_id());
        
        if (count($target_user_ids) > 0)
        {
            $granted_rights = RepositoryRights :: get_instance()->get_granted_rights_for_rights_entity_item(
                \Chamilo\Core\Repository\Manager :: context(),
                UserEntity :: ENTITY_TYPE, 
                $target_user_ids[0], 
                $location);
        }
        elseif (count($target_group_ids) > 0)
        {
            $granted_rights = RepositoryRights :: get_instance()->get_granted_rights_for_rights_entity_item(
                \Chamilo\Core\Repository\Manager :: context(),
                PlatformGroupEntity :: ENTITY_TYPE, 
                $target_group_ids[0], 
                $location);
        }
        
        $copy_right = array_search(RepositoryRights :: COPY_RIGHT, $granted_rights);
        
        if ($copy_right)
        {
            array_splice($granted_rights, $copy_right, 1);
            $copy_right = true;
        }
        else
        {
            $copy_right = false;
        }
        
        $this->setDefaults(array(self :: PARAM_RIGHT => max($granted_rights), self :: PARAM_COPY => $copy_right));
    }
}
