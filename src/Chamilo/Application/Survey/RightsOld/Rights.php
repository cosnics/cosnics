<?php
namespace Chamilo\Application\Survey\Rights;

use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Translation;

class Rights extends RightsUtil
{
    const TYPE_PUBLICATION = 1;
    const TREE_TYPE_PUBLICATION = 1;
    const PUBLISH_RIGHT = 1;
    const PUBLISH_RIGHT_NAME = 'publish';
    const PARTICIPATE_RIGHT = 2;
    const PARTICIPATE_RIGHT_NAME = 'participate';
    const INVITE_RIGHT = 3;
    const INVITE_RIGHT_NAME = 'invite';
    const MAIL_RIGHT = 4;
    const MAIL_RIGHT_NAME = 'mail';
    const RIGHT_VIEW = 5;
    const VIEW_RIGHT_NAME = 'view';
    const VIEW_EXPORT_NAME = 'export';
    const RIGHT_EDIT = 6;
    const EDIT_RIGHT_NAME = 'edit';
    const RIGHT_DELETE = 7;
    const DELETE_RIGHT_NAME = 'delete';
    const RIGHT_REPORTING = 8;
    const REPORTING_RIGHT_NAME = 'reporting';
    const RIGHT_EXPORT_RESULT = 9;
    const EXPORT_RESULT_RIGHT_NAME = 'export_result';
    const RIGHT_ACTIVATE = 10;
    const ACTIVATE_RIGHT_NAME = 'activate';
    const RIGHT_ADD_REPORTING_TEMPLATE = 11;
    const ADD_REPORTING_TEMPLATE_RIGHT_NAME = 'add_reporting_template';
    const RIGHT_ADD_EXPORT_TEMPLATE = 12;
    const ADD_EXPORT_TEMPLATE_RIGHT_NAME = 'add_export_template';

    private static $instance;

    private static $target_users;

    /**
     *
     * @return \application\survey\core\rights\Rights
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    static function get_available_rights_for_publications()
    {
        return array(
            Translation :: get(self :: PARTICIPATE_RIGHT_NAME) => self :: PARTICIPATE_RIGHT, 
            Translation :: get(self :: INVITE_RIGHT_NAME) => self :: INVITE_RIGHT, 
            Translation :: get(self :: MAIL_RIGHT_NAME) => self :: MAIL_RIGHT, 
            Translation :: get(self :: EDIT_RIGHT_NAME) => self :: RIGHT_EDIT, 
            Translation :: get(self :: DELETE_RIGHT_NAME) => self :: RIGHT_DELETE, 
            Translation :: get(self :: REPORTING_RIGHT_NAME) => self :: RIGHT_REPORTING, 
            Translation :: get(self :: ADD_REPORTING_TEMPLATE_RIGHT_NAME) => self :: RIGHT_ADD_REPORTING_TEMPLATE, 
            Translation :: get(self :: EXPORT_RESULT_RIGHT_NAME) => self :: RIGHT_EXPORT_RESULT, 
            Translation :: get(self :: ADD_EXPORT_TEMPLATE_RIGHT_NAME) => self :: RIGHT_ADD_EXPORT_TEMPLATE);
    }

    static function get_available_rights_for_reporting_template_registrations()
    {
        return array(Translation :: get(self :: VIEW_RIGHT_NAME) => self :: RIGHT_VIEW);
    }

    static function get_available_rights_for_export_templates()
    {
        return array(Translation :: get(self :: VIEW_EXPORT_NAME) => self :: RIGHT_VIEW);
    }

    public function is_right_granted($right, $publication_id, $user_id = null)
    {
        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();
        
        return self :: is_allowed(
            $right, 
            __NAMESPACE__, 
            $user_id, 
            $entities, 
            $publication_id, 
            self :: TYPE_PUBLICATION, 
            0, 
            self :: TREE_TYPE_PUBLICATION);
    }

    public function publication_is_allowed()
    {
        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();
        
        return self :: is_allowed(
            self :: PUBLISH_RIGHT, 
            __NAMESPACE__, 
            null, 
            $entities, 
            0, 
            self :: TYPE_ROOT, 
            0, 
            self :: TREE_TYPE_ROOT);
    }

    public function create_publication_location($publication_id)
    {
        return self :: create_location(
            __NAMESPACE__, 
            self :: TYPE_PUBLICATION, 
            $publication_id, 
            0, 
            $this->get_publication_root(), 
            0, 
            0, 
            self :: TREE_TYPE_PUBLICATION, 
            true);
    }

    public function get_publication_location($publication_id)
    {
            
        return self :: get_location_by_identifier(
            __NAMESPACE__, 
            self :: TYPE_PUBLICATION, 
            $publication_id, 
            0, 
            self :: TREE_TYPE_PUBLICATION);
    }

    public function get_publication_location_id($publication_id)
    {
        return self :: get_location_id_by_identifier(
            __NAMESPACE__, 
            self :: TYPE_PUBLICATION, 
            $publication_id, 
            0, 
            self :: TREE_TYPE_PUBLICATION);
    }

    public function set_publication_user_right($right, $user_id, $publication_id)
    {
        $location_id = $this->get_publication_location_id($publication_id);
        if ($location_id == 0)
        {
            $location = self :: get_instance()->create_publication_location($publication_id);
            $location_id = $location->get_id();
        }
        
        return self :: set_location_entity_right(
            __NAMESPACE__, 
            $right, 
            $user_id, 
            UserEntity :: ENTITY_TYPE, 
            $location_id);
    }

    public function set_publication_group_right($right, $group_id, $publication_id)
    {
        $location_id = $this->get_publication_location_id($publication_id);
        if ($location_id == 0)
        {
            $location = self :: get_instance()->create_publication_location($publication_id);
            $location_id = $location->get_id();
        }
        
        return self :: set_location_entity_right(
            __NAMESPACE__, 
            $right, 
            $group_id, 
            PlatformGroupEntity :: ENTITY_TYPE, 
            $location_id);
    }

    public function get_application_publish_rights_location_entity_right($entity_id, $entity_type)
    {
        return parent :: get_rights_location_entity_right(
            __NAMESPACE__, 
            self :: PUBLISH_RIGHT, 
            $entity_id, 
            $entity_type, 
            self :: get_application_root_id());
    }

    public function invert_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent :: invert_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }

    public function get_publication_targets_entities($right_id, $publication_id)
    {
        return parent :: get_target_entities(
            $right_id, 
            __NAMESPACE__, 
            $publication_id, 
            self :: TYPE_PUBLICATION, 
            0, 
            self :: TREE_TYPE_PUBLICATION);
    }

    public function get_application_targets_entities($right)
    {
        return parent :: get_target_entities($right, __NAMESPACE__);
    }

    public function get_publication_root()
    {
        return parent :: get_root(__NAMESPACE__, self :: TREE_TYPE_PUBLICATION, 0);
    }

    public function get_publication_root_id()
    {
        return parent :: get_root_id(__NAMESPACE__, self :: TREE_TYPE_PUBLICATION, 0);
    }

    public function create_publication_root()
    {
        return parent :: create_location(__NAMESPACE__, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_PUBLICATION);
    }

    public function get_application_root()
    {
        return parent :: get_root(__NAMESPACE__);
    }

    public function get_application_root_id()
    {
        return parent :: get_root_id(__NAMESPACE__);
    }

    public function create_application_root()
    {
        return parent :: create_location(__NAMESPACE__);
    }

    public function get_publication_location_entity_right($right, $entity_id, $entity_type, $publication_id)
    {
        $location_id = $this->get_publication_location_id($publication_id);
        
        return \Chamilo\Core\Rights\Storage\DataManager :: retrieve_rights_location_entity_right(
            __NAMESPACE__, 
            $right, 
            $entity_id, 
            $entity_type, 
            $location_id);
    }

    public function get_publication_ids_for_granted_right($right_id, $entities)
    {
        return self :: get_identifiers_with_right_granted(
            $right_id, 
            __NAMESPACE__, 
            $this->get_publication_root(), 
            self :: TYPE_PUBLICATION, 
            null, 
            $entities);
    }
}