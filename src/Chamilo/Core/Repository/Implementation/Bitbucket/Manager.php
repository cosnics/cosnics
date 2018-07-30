<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author magali.gillard
 */
abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'bitbucket';
    const PARAM_EXTERNAL_REPOSITORY_ACTION = 'action';
    const PARAM_EXTERNAL_REPOSITORY_USER = 'user';
    const PARAM_EXTERNAL_REPOSITORY_GROUP = 'group';
    const PARAM_EXTERNAL_REPOSITORY_PRIVILEGE = 'privilege';
    const ACTION_REVOKE_EXTERNAL_REPOSITORY_PRIVILEGE = 'Revoker';
    const ACTION_GRANT_EXTERNAL_REPOSITORY_PRIVILEGE = 'Granter';
    const ACTION_MULTI_GRANT_EXTERNAL_REPOSITORY_PRIVILEGE = 'MultiGranter';
    const ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES = 'PrivilegesViewer';
    const ACTION_GROUPS_VIEWER = 'GroupsViewer';
    const ACTION_DELETE_EXTERNAL_REPOSITORY_GROUP = 'GroupDeleter';
    const ACTION_CREATE_GROUP = 'GroupCreator';
    const ACTION_ADD_USER_TO_GROUP = 'AdderUserGgroup';
    const ACTION_DELETE_USER_FROM_GROUP = 'DeleterUserGroup';
    const ACTION_CREATE_REPOSITORY = 'Creator';
    const ACTION_EDIT_EXTERNAL_REPOSITORY = 'Editor';
    const ACTION_RENDER_REPOSITORY_FEED = 'RepositoryFeeder';
    const TYPE_OWN = 1;
    const TYPE_OTHER = 2;

    /**
     *
     * @param $applicationConfiguration ApplicationConfigurationInterface
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->set_parameter(self::PARAM_FOLDER, Request::get(self::PARAM_FOLDER));
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $username = Setting::get('username', $external_repository->get_id());
        $password = Setting::get('password', $external_repository->get_id());
        
        if (! $username || ! $password)
        {
            return false;
        }
        return true;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */
    public function support_sorting_direction()
    {
        return true;
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        
        return $this->get_url($parameters);
    }

    public function get_external_repository_privilege_revoking_url($id, $user)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_REVOKE_EXTERNAL_REPOSITORY_PRIVILEGE;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_USER] = $user;
        
        return $this->get_url($parameters);
    }

    public function get_external_repository_group_privilege_revoking_url($id, $group)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_REVOKE_EXTERNAL_REPOSITORY_PRIVILEGE;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_GROUP] = $group;
        
        return $this->get_url($parameters);
    }

    public function get_external_repository_privilege_granting_url($id, $users, $privilege)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_GRANT_EXTERNAL_REPOSITORY_PRIVILEGE;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_USER] = $users;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_PRIVILEGE] = $privilege;
        
        return $this->get_url($parameters);
    }

    public function get_external_repository_object_privileges_viewing_url(ExternalObject $object)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        
        return $this->get_url($parameters);
    }

    public function get_external_repository_group_deleting_url($group_id)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_EXTERNAL_REPOSITORY_GROUP;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_GROUP] = $group_id;
        return $this->get_url($parameters);
    }

    public function get_external_repository_group_creating_url()
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_GROUP;
        return $this->get_url($parameters);
    }

    public function get_external_repository_adding_user_url($group)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_ADD_USER_TO_GROUP;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_GROUP] = $group;
        return $this->get_url($parameters);
    }

    public function get_external_repository_deleting_user_url($group)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_USER_FROM_GROUP;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_GROUP] = $group;
        return $this->get_url($parameters);
    }

    public function get_external_repository_object_deleting_url($object)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        return $this->get_url($parameters);
    }

    public function get_external_repository_object_editing_url($object)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_EDIT_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        return $this->get_url($parameters);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_menu_items()
     */
    public function get_menu_items()
    {
        $menu_items = array();
        
        $my_repositories = array();
        $my_repositories['title'] = Translation::get('MyRepositories');
        $my_repositories['url'] = $this->get_url(
            array(self::PARAM_FOLDER => self::TYPE_OWN), 
            array(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $my_repositories['class'] = 'user';
        $menu_items[] = $my_repositories;
        
        $others_repositories = array();
        $others_repositories['title'] = Translation::get('OthersRepositories');
        $others_repositories['url'] = $this->get_url(
            array(self::PARAM_FOLDER => self::TYPE_OTHER, ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY => 'chamilo'));
        $others_repositories['class'] = 'home';
        $menu_items[] = $others_repositories;
        
        return $menu_items;
    }

    public function get_external_repository_object_actions(\Chamilo\Core\Repository\External\ExternalObject $object)
    {
        if ($object->is_editable())
        {
            $toolbar_items[self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES] = new ToolbarItem(
                Translation::get('ViewPrivileges'), 
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Action/ViewPrivileges'), 
                $this->get_external_repository_object_privileges_viewing_url($object), 
                ToolbarItem::DISPLAY_ICON);
            $toolbar_items[self::ACTION_EDIT_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation::get('EditRepository'), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_external_repository_object_editing_url($object), 
                ToolbarItem::DISPLAY_ICON);
        }
        
        if ($object->is_deletable())
        {
            $toolbar_items[self::ACTION_DELETE_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation::get('DeleteRepository'), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_external_repository_object_deleting_url($object), 
                ToolbarItem::DISPLAY_ICON, 
                true);
        }
        
        return $toolbar_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array();
        $actions[] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $actions[] = self::ACTION_MULTI_GRANT_EXTERNAL_REPOSITORY_PRIVILEGE;
        $actions[] = self::ACTION_GROUPS_VIEWER;
        $actions[] = self::ACTION_CREATE_REPOSITORY;
        
        $is_platform = $this->get_user()->is_platform_admin();
        $has_setting = $this->get_external_repository()->has_settings();
        
        if (! (! $has_setting || ! $is_platform))
        {
            $actions[] = self::ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }
        
        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self::REPOSITORY_TYPE;
    }
}
