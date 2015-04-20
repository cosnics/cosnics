<?php
namespace Chamilo\Core\Repository;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\FormFilterRenderer;
use Chamilo\Core\Repository\Filter\Renderer\HtmlFilterRenderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Menu\ObjectTypeMenu;
use Chamilo\Core\Repository\Menu\RepositoryCategoryTreeMenu;
use Chamilo\Core\Repository\Menu\RepositoryMenu;
use Chamilo\Core\Repository\Menu\SharedRepositoryCategoryTreeMenu;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Menu\UserViewMenu;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.lib.repository_manager A repository manager provides some functionalities to the end user to
 *          manage his objects in the repository. For each functionality a component is available.
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'repository';

    /**
     * #@+ Constant defining a parameter of the repository manager.
     */
    // SortableTable hogs 'action' so we'll use something else.
    const PARAM_CATEGORY_ID = 'category';
    const PARAM_SHARED_CATEGORY_ID = 'shared_category_id';
    const PARAM_CONTENT_OBJECT_ID = 'object';
    const PARAM_ATTACHMENT_ID = 'attachment';
    const PARAM_DESTINATION_CONTENT_OBJECT_ID = 'destination';
    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PARAM_CONTENT_OBJECT_TEMPLATE_REGISTRATION_ID = 'template_id';
    const PARAM_DELETE_PERMANENTLY = 'delete_permanently';
    const PARAM_DELETE_VERSION = 'delete_version';
    const PARAM_DELETE_RECYCLED = 'delete_recycle';
    const PARAM_EXPORT_SELECTED = 'export_selected';
    const PARAM_EXPORT_CP_SELECTED = 'export_cp_selected';
    const PARAM_EMPTY_RECYCLE_BIN = 'empty';
    const PARAM_RECYCLE_SELECTED = 'recycle_selected';
    const PARAM_MOVE_SELECTED = 'move_selected';
    const PARAM_RESTORE_SELECTED = 'restore_selected';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_EDIT_SELECTED_RIGHTS = 'rights_selected';
    const PARAM_PUBLISH_SELECTED = 'publish_selected';
    const PARAM_COMPARE_OBJECT = 'object';
    const PARAM_COMPARE_VERSION = 'compare';
    const PARAM_CLOI_REF = 'cloi_ref';
    const PARAM_CLOI_ID = 'cloi_id';
    const PARAM_CLOI_ROOT_ID = 'cloi_root_id';
    const PARAM_CLOI_COMPLEX_REF = 'cloi_complex_ref';
    const PARAM_DISPLAY_ORDER = 'display_order';
    const PARAM_REMOVE_SELECTED_CLOI = 'cloi_delete_selected';
    const PARAM_MOVE_DIRECTION = 'move_direction';
    const PARAM_DIRECTION_UP = 'up';
    const PARAM_DIRECTION_DOWN = 'down';
    const PARAM_ADD_OBJECTS = 'add_objects';
    const PARAM_TARGET_USER = 'target_user';
    const PARAM_TARGET_GROUP = 'target_group';
    const PARAM_COPY_TO_TEMPLATES = 'copy_to_template';
    const PARAM_EXTERNAL_OBJECT_ID = 'external_object_id';
    const PARAM_EXTERNAL_REPOSITORY_ID = 'ext_rep_id';
    const PARAM_EXTERNAL_INSTANCE = 'external_instance';
    const PARAM_LINK_TYPE = 'link_type';
    const PARAM_LINK_ID = 'link_id';
    const PARAM_CONTENT_OBJECT_MANAGER_TYPE = 'manage';
    const PARAM_SHARED_VIEW = 'shared_view';
    const PARAM_SHOW_OBJECTS_SHARED_BY_ME = 'show_my_objects';
    const PARAM_RENAME = 'rename_co';
    const PARAM_EXPORT_TYPE = 'export_type';
    const PARAM_IMPORT_TYPE = 'import_type';
    const PARAM_CATEGORY_TYPE = 'category_type';
    const PARAM_TYPE = 'type';
    const PARAM_IDENTIFIER = 'identifier';
    const SHARED_VIEW_OTHERS_OBJECTS = 0;
    const SHARED_VIEW_OWN_OBJECTS = 1;
    const SHARED_VIEW_ALL_OBJECTS = 2;
    const PARAM_RENDERER = 'renderer';

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_BROWSE_CONTENT_OBJECTS = 'Browser';
    const ACTION_BROWSE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsBrowser';
    const ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS = 'RecycleBinBrowser';
    const ACTION_VIEW_CONTENT_OBJECTS = 'Viewer';
    const ACTION_CREATE_CONTENT_OBJECTS = 'Creator';
    const ACTION_EDIT_CONTENT_OBJECTS = 'Editor';
    const ACTION_REVERT_CONTENT_OBJECTS = 'Reverter';
    const ACTION_DELETE_CONTENT_OBJECTS = 'Deleter';
    const ACTION_DELETE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsDeleter';
    const ACTION_UNLINK_CONTENT_OBJECTS = 'Unlinker';
    const ACTION_RESTORE_CONTENT_OBJECTS = 'Restorer';
    const ACTION_MOVE_CONTENT_OBJECTS = 'Mover';
    const ACTION_MOVE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsMover';
    const ACTION_QUOTA = 'Quota';
    const ACTION_COMPARE_CONTENT_OBJECTS = 'Comparer';
    const ACTION_EXPORT_CONTENT_OBJECTS = 'Exporter';
    const ACTION_IMPORT_CONTENT_OBJECTS = 'Importer';
    const ACTION_MANAGE_CATEGORIES = 'CategoryManager';
    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'Builder';
    const ACTION_VIEW_REPO = 'AttachmentViewer';
    const ACTION_DOWNLOAD_DOCUMENT = 'DocumentDownloader';
    const ACTION_COPY_CONTENT_OBJECT = 'Copier';
    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    const ACTION_DELETE_LINK = 'LinkDeleter';
    const ACTION_VIEW_DOUBLES = 'DoublesViewer';
    const ACTION_EXTERNAL_INSTANCE_MANAGER = 'ExternalInstance';
    const ACTION_MANAGE_EXTERNAL_INSTANCES = 'ExternalInstanceManager';
    const ACTION_SHARE_CONTENT_OBJECTS = 'ShareContentObjects';
    const ACTION_HTML_EDITOR_FILE = 'HtmlEditorFile';
    const ACTION_REPOSITORY_VIEWER = 'RepositoryViewer';
    const ACTION_USER_VIEW = 'UserView';
    const ACTION_PREVIEW = 'Previewer';
    const ACTION_PUBLICATION = 'Publication';
    const ACTION_LINK_CONTENT_OBJECT_PROPERTY_METADATA = 'PropertyMetadataLinker';
    const ACTION_LINK_CONTENT_OBJECT_METADATA_ELEMENT = 'MetadataElementLinker';
    const ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE = 'AlternativeLinker';
    const ACTION_BATCH_EDIT_CONTENT_OBJECT_METADATA = 'MetadataBatchEditor';
    const ACTION_TEMPLATE = 'Template';
    const ACTION_LINK_SCHEMAS = 'SchemaLinker';

    // Tabs
    const TABS_FILTER = 'advanced_filter';
    const TABS_CONTENT_OBJECT = 'content_object';

    const TAB_CATEGORY = 'Category';
    const TAB_OBJECT_TYPE = 'ObjectType';
    const TAB_SEARCH = 'Search';
    const TAB_USERVIEW = 'Userview';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE_CONTENT_OBJECTS;

    /**
     * Property of this repository manager.
     */
    private $search_parameters;

    private $search_form;

    private $category_menu;

    private $shared_category_menu;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null)
    {
        parent :: __construct($request, $user, $application);

        if (! is_null($user) && $user->is_anonymous_user())
        {
            throw new NotAllowedException();
        }

        $this->set_optional_parameters();
    }

    public function set_optional_parameters()
    {
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
        $this->set_parameter(
            DynamicTabsRenderer :: PARAM_SELECTED_TAB,
            Request :: get(DynamicTabsRenderer :: PARAM_SELECTED_TAB));
    }

    public function has_menu()
    {
        return $this->get_action() != self :: ACTION_EXTERNAL_INSTANCE_MANAGER &&
             $this->get_action() != self :: ACTION_MANAGE_EXTERNAL_INSTANCES;
    }

    public function get_menu()
    {
        $html = array();

        $html[] = '<div id="repository_tree_container">';
        $tabs = new DynamicTabsRenderer(self :: TABS_FILTER);

        $hide_sharing = PlatformSetting :: get('hide_sharing', __NAMESPACE__) === 1 ? true : false;
        if (! $hide_sharing)
        {
            $shared_category_menu = $this->get_shared_category_menu()->render_as_tree();
        }

        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_CATEGORY,
                '',
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self :: TAB_CATEGORY),
                $this->get_category_menu()->render_as_tree() . '<br />' . $shared_category_menu));

        $filter_form = FormFilterRenderer :: factory(
            FilterData :: get_instance(),
            $this->get_user_id(),
            $this->get_allowed_content_object_types(),
            $this->get_url(
                array(
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_SEARCH),
                    self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS),
                array(self :: PARAM_CATEGORY_ID)));

        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_SEARCH,
                '',
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self :: TAB_SEARCH),
                $filter_form->render()));

        $selected_type = FilterData :: get_instance()->get_type();
        $selected_category = FilterData :: get_instance()->get_type_category();

        $object_type = new ObjectTypeMenu(
            $this,
            $selected_type,
            $this->get_url(
                array(
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_OBJECT_TYPE),
                    FilterData :: FILTER_TYPE => '__SELECTION__',
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_OBJECT_TYPE),
                    Application :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS),
                array(self :: PARAM_CATEGORY_ID, self :: PARAM_CONTENT_OBJECT_ID)),
            $selected_category,
            $this->get_url(
                array(
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_OBJECT_TYPE),
                    FilterData :: FILTER_TYPE => '__CATEGORY__',
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_OBJECT_TYPE),
                    Application :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS),
                array(self :: PARAM_CATEGORY_ID, self :: PARAM_CONTENT_OBJECT_ID)));

        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_OBJECT_TYPE,
                '',
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self :: TAB_OBJECT_TYPE),
                $object_type->render_as_tree()));

        $current_user_view_id = FilterData :: get_instance()->get_user_view();
        $user_view = new UserViewMenu(
            $this,
            $current_user_view_id,
            $this->get_url(
                array(
                    FilterData :: FILTER_USER_VIEW => '__VIEW__',
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_USERVIEW))));
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_USERVIEW,
                '',
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self :: TAB_USERVIEW),
                $user_view->render_as_tree()));

        $html[] = ($tabs->render());

        $html_filter_renderer = HtmlFilterRenderer :: factory(FilterData :: get_instance());

        $html[] = $html_filter_renderer->render();

        $repository_menu = new RepositoryMenu($this);
        $html[] = ($repository_menu->render_as_tree());

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the parameter list
     *
     * @param $include_search boolean Include the search parameters in the returned list?
     * @return array The list of parameters.
     */
    public function get_parameters($include_search = false)
    {
        if ($include_search && isset($this->search_parameters))
        {
            return array_merge($this->search_parameters, parent :: get_parameters());
        }

        return parent :: get_parameters();
    }

    /**
     * Gets the value of a search parameter.
     *
     * @param $name string The search parameter name.
     * @return string The search parameter value.
     */
    public function get_search_parameter($name)
    {
        return $this->search_parameters[$name];
    }

    /**
     * Sets the active URL in the navigation menu.
     *
     * @param $url string The active URL.
     */
    public function force_menu_url($url)
    {
        $this->get_category_menu()->forceCurrentUrl($url);
    }

    /**
     * Gets the URL to the quota page.
     *
     * @return string The URL.
     */
    public function get_quota_url()
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_QUOTA,
                self :: PARAM_CATEGORY_ID => null,
                \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null,
                DynamicTabsRenderer :: PARAM_SELECTED_TAB => null));
    }

    /**
     * Gets the URL to the publication page.
     *
     * @return string The URL.
     */
    public function get_publication_url()
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_PUBLICATION),
            array(\Chamilo\Core\Repository\Publication\Manager :: PARAM_ACTION),
            false);
    }

    /**
     * Gets the URL to the object creation page.
     *
     * @return string The URL.
     */
    public function get_content_object_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECTS));
    }

    /**
     * Gets the URL to the object import page.
     *
     * @return string The URL.
     */
    public function get_content_object_importing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_CONTENT_OBJECTS));
    }

    /**
     * Gets the URL to the recycle bin.
     *
     * @return string The URL.
     */
    public function get_recycle_bin_url()
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS,
                self :: PARAM_CATEGORY_ID => null));
    }

    /**
     * Gets the id of the root category.
     *
     * @return integer The requested id.
     */
    public function get_root_category_id()
    {
        /*
         * if (isset ($this->category_menu)) { return $this->category_menu->_menu[0][OptionsMenuRenderer :: KEY_ID]; }
         * else { $dm = RepositoryDataManager :: get_instance(); $cat =
         * $dm->retrieve_root_category($this->get_user_id()); return $cat->get_id(); }
         */
        return 0;
    }

    /**
     * Retrieves a object.
     *
     * @param $id int The id of the object.
     * @param $type string The type of the object. Default is null. If you know the type of the requested object, you
     *        should give it as a parameter as this will make object retrieval faster.
     */
    public function retrieve_content_object($id, $type = null)
    {
        return DataManager :: retrieve_content_object($id, $type);
    }

    public function retrieve_content_object_versions_resultset($condition = null, $order_by = array (), $offset = 0,
        $max_objects = -1)
    {
        return DataManager :: retrieve_content_objects(ContentObject :: class_name(), $condition);
    }

    public function count_content_object_versions_resultset($condition = null)
    {
        return DataManager :: count_content_objects(ContentObject :: class_name(), $condition);
    }

    /**
     *
     * @see DataManager::retrieve_type_content_objects()
     */
    public function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return DataManager :: retrieve_active_content_objects($type, $condition);
    }

    /**
     *
     * @see DataManager::get_version_ids()
     */
    // function get_version_ids($object)
    // {
    // return DataManager :: get_version_ids($object);
    // }
    public function count_type_content_objects($type, $condition = null)
    {
        return DataManager :: count_active_content_objects($type, $condition);
    }

    /**
     *
     * @see DataManager::content_object_deletion_allowed()
     */
    public function content_object_deletion_allowed($content_object, $type = null)
    {
        return DataManager :: content_object_deletion_allowed($content_object, $type);
    }

    /**
     *
     * @see DataManager::content_object_revert_allowed()
     */
    public function content_object_revert_allowed($content_object)
    {
        return DataManager :: content_object_revert_allowed($content_object);
    }

    public function get_registered_types()
    {
        return DataManager :: get_registered_types();
    }

    /**
     *
     * @see DataManager::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($id, $application)
    {
        return DataManager :: get_content_object_publication_attribute($id, $application);
    }

    /**
     * Gets the url to view a object.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_viewing_url($content_object)
    {
        if ($content_object->get_state() == ContentObject :: STATE_RECYCLED)
        {
            return $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS,
                    self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                    self :: PARAM_CATEGORY_ID => null));
        }

        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                FilterData :: FILTER_CATEGORY => $content_object->get_parent_id()));
    }

    public function get_external_instance_viewing_url(SynchronizationData $external_instance_sync)
    {
        if (! $external_instance_sync || ! $external_instance_sync->get_external())
        {
            return;
        }

        $parameters = \Chamilo\Core\Repository\External\Manager :: get_object_viewing_parameters(
            $external_instance_sync);
        $parameters[self :: PARAM_CONTEXT] = $external_instance_sync->get_external()->get_type();
        $parameters[self :: PARAM_EXTERNAL_INSTANCE] = $external_instance_sync->get_external_id();

        return $this->get_url($parameters);
    }

    /**
     * Gets the url to view a object.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_editing_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    public function get_content_object_unlinker_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_UNLINK_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to recycle a object (move the object to the recycle bin).
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_recycling_url($content_object, $force = false)
    {
        // if (! $this->content_object_deletion_allowed($content_object) ||
        // $content_object->get_state() == ContentObject :: STATE_RECYCLED)
        // {
        // return null;
        // }
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                self :: PARAM_DELETE_RECYCLED => 1));
    }

    /**
     * Gets the url to restore a object from the recycle bin.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_restoring_url($content_object)
    {
        if ($content_object->get_state() != ContentObject :: STATE_RECYCLED)
        {
            return null;
        }
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_RESTORE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to delete a object from recycle bin.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_deletion_url($content_object, $type = null)
    {
        if (! $this->content_object_deletion_allowed($content_object, $type))
        {
            return null;
        }

        if (isset($type))
        {
            $param = self :: PARAM_DELETE_VERSION;
        }
        else
        {
            if ($content_object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $param = self :: PARAM_DELETE_PERMANENTLY;
            }
            else
            {
                $param = self :: PARAM_DELETE_RECYCLED;
            }
        }
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                $param => 1));
    }

    /**
     * Gets the url to revert to a object version.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_revert_url($content_object)
    {
        if (! $this->content_object_revert_allowed($content_object))
        {
            return null;
        }

        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_REVERT_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to move a object to another category.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_moving_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the defined object types
     *
     * @see DataManager::get_registered_types()
     * @param $only_master_types boolean Only return the master type objects (which can exist on their own). Returns all
     *        object types by default.
     */
    public function get_content_object_types($check_view_right = true)
    {
        return DataManager :: get_registered_types($check_view_right);
    }

    public function get_allowed_content_object_types()
    {
        $types = $this->get_content_object_types(true, false);

        foreach ($types as $index => $type)
        {
            $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration(
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type));

            if (! $registration || ! $registration->is_active())
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    /**
     * Gets some user information
     *
     * @param $id int The user id
     * @return The user
     */
    public function get_user_info($user_id)
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(User :: class_name(), (int) $user_id);
    }

    /**
     * Gets the url for browsing objects of a given type
     *
     * @param int $template_registration_id
     * @return string The url
     */
    public function get_type_filter_url($template_registration_id)
    {
        $params = array();
        $params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_CONTENT_OBJECTS;
        $params[FilterData :: FILTER_TYPE] = $template_registration_id;
        return $this->get_url($params);
    }

    /**
     * Gets the condition to select only objects in the given category of any subcategory.
     * Note that this will also
     * initialize the category menu to one with the "Search Results" item, if this has not happened already.
     *
     * @param $category_id int The category
     * @return Condition
     */
    public function get_category_condition($category_id)
    {
        $subcat = array();
        $this->get_category_id_list($category_id, $this->get_category_menu(true)->_menu, $subcat);
        $conditions = array();
        foreach ($subcat as $cat)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_PARENT_ID),
                new StaticConditionVariable($cat));
        }
        return (count($conditions) > 1 ? new OrCondition($conditions) : $conditions[0]);
    }

    /**
     *
     * @todo Move this to ContentObjectCategoryMenu or something.
     */
    private function get_category_id_list($category_id, $node, $subcat)
    {
        // XXX: Make sure we don't mess up things with trash here.
        foreach ($node as $id => $subnode)
        {
            $new_id = ($id == $category_id ? null : $category_id);
            // Null means we've reached the category we want, so we add.
            if (is_null($new_id))
            {
                $subcat[] = $id;
            }
            $this->get_category_id_list($new_id, $subnode['sub'], $subcat);
        }
    }

    /**
     * Gets the category menu.
     * This menu contains all categories in the repository of the current user. Additionally
     * some menu items are added - Recycle Bin - Create a new object - Quota - Search Results (ony if search is
     * performed)
     *
     * @param $force_search boolean Whether the user is searching. If true, overrides the default, which is to request
     *        this information from the search form.
     * @return ContentObjectCategoryMenu The menu
     */
    private function get_category_menu($force_search = false)
    {
        $this->set_parameter(
            DynamicTabsRenderer :: PARAM_SELECTED_TAB,
            array(self :: TABS_FILTER => self :: TAB_CATEGORY));

        if (! isset($this->category_menu))
        {
            if ($force_search)
            {
                $search_url = '#';
            }
            else
            {
                $search_url = null;
            }

            $this->category_menu = new RepositoryCategoryTreeMenu($this);

            if (isset($search_url))
            {
                $this->category_menu->forceCurrentUrl($search_url, true);
            }
            if ($this->get_action() != self :: ACTION_BROWSE_CONTENT_OBJECTS)
            {
                $this->category_menu->forceCurrentUrl($this->get_url());
            }
        }
        return $this->category_menu;
    }

    /**
     * Returns the category menu for shared items
     *
     * @return RepositoryCategory
     */
    private function get_shared_category_menu()
    {
        if (! $this->shared_category_menu)
        {
            $extra_items = array();

            $shared_own = array();
            $shared_own['title'] = Translation :: get('ContentObjectsSharedByMe');
            $shared_own['url'] = $this->get_shared_content_objects_url(self :: SHARED_VIEW_OWN_OBJECTS);
            $shared_own['class'] = 'category';
            $extra_items[] = $shared_own;

            $this->shared_category_menu = new SharedRepositoryCategoryTreeMenu($this, $extra_items);

            /**
             * Fix for the selected menu item when the content objects shared by me is selected because this is an
             * additional item that is not selected by default.
             */
            if (Request :: get(self :: PARAM_ACTION) != self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS)
            {
                $this->shared_category_menu->forceCurrentUrl('');
            }
            else
            {
                if (Request :: get(self :: PARAM_SHARED_VIEW) == self :: SHARED_VIEW_OWN_OBJECTS)
                {
                    $this->shared_category_menu->forceCurrentUrl($shared_own['url']);
                }
            }
        }

        return $this->shared_category_menu;
    }

    /**
     * Return a condition object that can be used to look for objects of the current logged user that are recycled
     *
     * @return AndCondition
     */
    public function get_current_user_recycle_bin_conditions()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable(ContentObject :: STATE_RECYCLED));

        return new AndCondition($conditions);
    }

    /**
     *
     * @return boolean
     */
    public function current_user_has_recycled_objects()
    {
        $parameters = new DataClassCountParameters($this->get_current_user_recycle_bin_conditions());
        return DataManager :: count_active_content_objects(ContentObject :: class_name(), $parameters) > 0;
    }

    /**
     * Displays the tree menu.
     */
    private function display_content_object_categories()
    {
        return $this->get_category_menu()->render_as_tree();
    }

    public static function get_document_downloader_url($document_id)
    {
        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($document_id);
        if ($object)
        {
            $security_code = $object->calculate_security_code();
        }

        $redirect = new Redirect(
            array(
                self :: PARAM_CONTEXT => self :: context(),
                self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENT,
                self :: PARAM_CONTENT_OBJECT_ID => $document_id,
                ContentObject :: PARAM_SECURITY_CODE => $security_code));

        return $redirect->getUrl();
    }

    public function get_complex_content_object_item_edit_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEMS,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                'publish' => Request :: get('publish')));
    }

    public function get_complex_content_object_item_delete_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEMS,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                'publish' => Request :: get('publish')));
    }

    public function get_complex_content_object_item_move_url($complex_content_object_item, $root_id, $direction)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEMS,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                self :: PARAM_MOVE_DIRECTION => $direction,
                'publish' => Request :: get('publish')));
    }

    public function get_browse_complex_content_object_url($object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT,
                self :: PARAM_CONTENT_OBJECT_ID => $object->get_id()));
    }

    public static function get_preview_content_object_url($content_object)
    {
        return \Chamilo\Core\Repository\Preview\Manager :: get_content_object_default_action_link($content_object);
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @return string
     * @deprecated Previews are no longer unique for complex objects, use
     *             <code>get_preview_content_object_url($content_object)</code> now.
     */
    public static function get_preview_complex_content_object_url($content_object)
    {
        return self :: get_preview_content_object_url($content_object);
    }

    public function get_add_existing_content_object_url($root_id, $complex_content_object_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SELECT_CONTENT_OBJECTS,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                'publish' => Request :: get('publish')));
    }

    public function get_add_content_object_url($content_object, $complex_content_object_item_id, $root_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_ADD_CONTENT_OBJECT,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_REF => $content_object->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                'publish' => Request :: get('publish')));
    }

    public function get_content_object_exporting_url($content_object, $type)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                self :: PARAM_EXPORT_TYPE => $type),
            array(self :: PARAM_CATEGORY_ID));
    }

    public function get_content_objects_exporting_url($type, $ids, $format)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT_CONTENT_OBJECTS,
                $type => $ids,
                self :: PARAM_EXPORT_TYPE => $format));
    }

    public function get_publish_content_object_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_PUBLICATION,
                \Chamilo\Core\Repository\Publication\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager :: ACTION_PUBLISH,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    public function get_content_object_metadata_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                self :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_EDIT_CONTENT_OBJECT_METADATA,
                \Chamilo\Core\Metadata\Manager :: PARAM_CONTENT_OBJECT => $content_object->get_id()));
    }

    public function get_content_object_alternative_linker($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /*
     * Returns the url of the attachment viewer. Used in rendition implementation @param ContentObject $attachment
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_ATTACHMENT_ID] = $attachment->get_id();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_ATTACHMENT;

        return $this->get_url($parameters);
    }

    public function count_categories($conditions = null)
    {
        return DataManager :: count(RepositoryCategory :: class_name(), $conditions);
    }

    // External instances
    public function retrieve_external_instance_condition($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(Instance :: class_name(), $parameters);
    }

    public function retrieve_external_instance($external_repository_id)
    {
        return \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            Instance :: class_name(),
            $external_repository_id);
    }

    public function retrieve_external_instances($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(Instance :: class_name(), $parameters);
    }

    public function count_external_instances($condition = null)
    {
        return \Chamilo\Core\Repository\Instance\Storage\DataManager :: count_content_objects(
            Instance :: class_name(),
            $condition);
    }

    public function get_shared_content_objects_url($view)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS,
                self :: PARAM_CATEGORY_ID => null,
                self :: PARAM_SHARED_VIEW => $view,
                DynamicTabsRenderer :: PARAM_SELECTED_TAB => array(self :: TABS_FILTER => self :: TAB_CATEGORY)));
    }

    public function get_copy_content_object_url($content_object_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_COPY_CONTENT_OBJECT,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object_id));
    }

    public function get_view_doubles_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_DOUBLES));
    }

    public function get_delete_link_url($type, $object_id, $link_id)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_LINK;
        $parameters[self :: PARAM_LINK_TYPE] = $type;
        $parameters[self :: PARAM_CONTENT_OBJECT_ID] = $object_id;
        $parameters[self :: PARAM_LINK_ID] = $link_id;

        return $this->get_url($parameters);
    }

    public function get_share_content_objects_url($content_object_ids)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SHARE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids));
    }

    public function get_create_share_content_objects_url($content_object_ids)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SHARE_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids,
                \Chamilo\Core\Repository\Share\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Share\Manager :: ACTION_ADD_ENTITIES));
    }

    /**
     * Gets the url to move a share to another share category.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_shared_content_object_moving_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_SHARED_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to delete a share
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_shared_content_object_deletion_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_SHARED_CONTENT_OBJECTS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    public function get_renderer()
    {
        $renderer = Request :: get(self :: PARAM_RENDERER);

        if ($renderer && in_array($renderer, $this->get_available_renderers()))
        {
            return $renderer;
        }
        else
        {
            $renderers = $this->get_available_renderers();
            return $renderers[0];
        }
    }

    public function get_available_renderers()
    {
        return array(
            ContentObjectRenderer :: TYPE_TABLE,
            ContentObjectRenderer :: TYPE_GALLERY,
            ContentObjectRenderer :: TYPE_SLIDESHOW);
    }

    public function get_external_instance_manager_url()
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_MANAGE_EXTERNAL_INSTANCES),
            array(\Chamilo\Core\Repository\Instance\Manager :: PARAM_INSTANCE_ACTION));
    }

    public function is_object_shared_with_me($object)
    {
        return DataManager :: is_object_shared_with_user($this->get_user(), $object);
    }

    /**
     *
     * @param string $content_object_type
     * @param string $template_registration_id
     * @return string
     */
    public function get_content_object_type_creation_url($template_registration_id)
    {
        return $this->get_url(array(TypeSelector :: PARAM_SELECTION => $template_registration_id));
    }
}
