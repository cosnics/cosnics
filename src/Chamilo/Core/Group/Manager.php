<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Group\Form\GroupSearchForm;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\User\Form\UserSearchForm;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package group.lib.group_manager
 */
abstract class Manager extends Application
{
    const PARAM_GROUP_ID = 'group_id';
    const PARAM_GROUP_REL_USER_ID = 'group_rel_user_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_COMPONENT_ACTION = 'action';
    const ACTION_CREATE_GROUP = 'Creator';
    const ACTION_BROWSE_GROUPS = 'Browser';
    const ACTION_EDIT_GROUP = 'Editor';
    const ACTION_DELETE_GROUP = 'Deleter';
    const ACTION_MOVE_GROUP = 'Mover';
    const ACTION_TRUNCATE_GROUP = 'Truncater';
    const ACTION_VIEW_GROUP = 'Viewer';
    const ACTION_EXPORT = 'Exporter';
    const ACTION_IMPORT = 'Importer';
    const ACTION_IMPORT_GROUP_USERS = 'GroupUserImporter';
    const ACTION_SUBSCRIBE_USER_TO_GROUP = 'Subscriber';
    const ACTION_SUBSCRIBE_USER_BROWSER = 'SubscribeUserBrowser';
    const ACTION_UNSUBSCRIBE_USER_FROM_GROUP = 'Unsubscriber';
    const ACTION_MANAGE_METADATA = 'MetadataManager';
    const DEFAULT_ACTION = self::ACTION_BROWSE_GROUPS;

    private $parameters;

    private $search_parameters;

    private $user_search_parameters;

    private $search_form;

    private $user_search_form;

    private $user_id;

    private $user;

    private $category_menu;

    private $quota_url;

    private $publication_url;

    private $create_url;

    private $recycle_bin_url;

    protected $breadcrumbs;

    /**
     * The currently selected group
     *
     * @var Group
     */
    private $selected_group;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->create_url = $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE_GROUP));

        $this->checkAuthorization(Manager::context());
    }

    public function count_groups($condition = null)
    {
        $parameters = new DataClassCountParameters($condition);

        return DataManager::count(Group::class_name(), $parameters);
    }

    public function count_group_rel_users($condition = null)
    {
        $parameters = new DataClassCountParameters($condition);

        return DataManager::count(GroupRelUser::class_name(), $parameters);
    }

    public function get_search_condition()
    {
        return $this->get_search_form()->get_condition();
    }

    public function get_user_search_condition()
    {
        return $this->get_user_search_form()->get_condition();
    }

    private function get_search_form()
    {
        if (! isset($this->search_form))
        {
            $this->search_form = new GroupSearchForm($this, $this->get_url());
        }

        return $this->search_form;
    }

    private function get_user_search_form()
    {
        if (! isset($this->user_search_form))
        {
            $this->user_search_form = new UserSearchForm(
                $this,
                $this->get_url(array(self::PARAM_GROUP_ID => Request::get(self::PARAM_GROUP_ID))));
        }

        return $this->user_search_form;
    }

    public function get_search_validate()
    {
        return $this->get_search_form()->validate();
    }

    public function get_user_search_validate()
    {
        return $this->get_user_search_form()->validate();
    }

    /**
     * Gets the parameter list
     *
     * @param boolean $include_search Include the search parameters in the returned list?
     * @return array The list of parameters.
     */
    public function get_parameters($include_search = false, $include_user_search = false)
    {
        $parms = parent::get_parameters();

        if ($include_search && isset($this->search_parameters))
        {
            $parms = array_merge($this->search_parameters, $parms);
        }

        if ($include_user_search && isset($this->user_search_parameters))
        {
            $parms = array_merge($this->user_search_parameters, $parms);
        }

        return $parms;
    }

    public function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public static function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager::retrieves(
            GroupRelUser::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public static function retrieve_group_rel_user($user_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group_id));
        $condition = new AndCondition($conditions);

        return DataManager::retrieve(GroupRelUser::class_name(), new DataClassRetrieveParameters($condition));
    }

    public function retrieve_group($id)
    {
        return DataManager::retrieve_by_id(Group::class_name(), $id);
    }

    public function get_group_editing_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_EDIT_GROUP, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_create_group_url($parent_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_CREATE_GROUP, self::PARAM_GROUP_ID => $parent_id));
    }

    public function get_group_emptying_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_TRUNCATE_GROUP, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_group_viewing_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW_GROUP, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_group_metadata_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_MANAGE_METADATA, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_group_rel_user_unsubscribing_url(GroupRelUser $groupreluser)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_USER_FROM_GROUP,
                self::PARAM_GROUP_REL_USER_ID => $groupreluser->getId()));
    }

    public function get_group_rel_user_subscribing_url($group, $user)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_TO_GROUP,
                self::PARAM_GROUP_ID => $group->get_id(),
                self::PARAM_USER_ID => $user->get_id()));
    }

    public function get_group_suscribe_user_browser_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_group_delete_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_DELETE_GROUP, self::PARAM_GROUP_ID => $group->get_id()));
    }

    public function get_import_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_IMPORT));
    }

    public function get_export_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_EXPORT));
    }

    public function get_move_group_url($group)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_MOVE_GROUP, self::PARAM_GROUP_ID => $group->get_id()));
    }

    /**
     * Returns the selected group
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return Group
     */
    protected function get_selected_group()
    {
        if (! isset($this->selected_group))
        {
            $group_id = Request::get(self::PARAM_GROUP_ID);
            if (! $group_id)
            {
                throw new NoObjectSelectedException(Translation::get('Group'));
            }

            $group = DataManager::retrieve_by_id(Group::class_name(), $group_id);
            if (! $group)
            {
                throw new ObjectNotExistException(Translation::get('Group', $group_id));
            }

            $this->selected_group = $group;
        }

        return $this->selected_group;
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
