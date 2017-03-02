<?php
namespace Chamilo\Core\Repository;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\FormFilterRenderer;
use Chamilo\Core\Repository\Filter\Renderer\HtmlFilterRenderer;
use Chamilo\Core\Repository\Menu\ObjectTypeMenu;
use Chamilo\Core\Repository\Menu\RepositoryCategoryTreeMenu;
use Chamilo\Core\Repository\Menu\RepositoryMenu;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Menu\UserViewMenu;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Dieter De Neef
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
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
    const PARAM_WORKSPACE_ID = 'workspace_id';
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
    const ACTION_HTML_EDITOR_FILE = 'HtmlEditorFile';
    const ACTION_REPOSITORY_VIEWER = 'RepositoryViewer';
    const ACTION_USER_VIEW = 'UserView';
    const ACTION_PREVIEW = 'Previewer';
    const ACTION_WORKSPACE = 'Workspace';
    const ACTION_PUBLICATION = 'Publication';
    const ACTION_LINK_CONTENT_OBJECT_PROPERTY_METADATA = 'PropertyMetadataLinker';
    const ACTION_LINK_CONTENT_OBJECT_METADATA_ELEMENT = 'MetadataElementLinker';
    const ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE = 'AlternativeLinker';
    const ACTION_BATCH_EDIT_CONTENT_OBJECT_METADATA = 'MetadataBatchEditor';
    const ACTION_LINK_SCHEMAS = 'SchemaLinker';
    const ACTION_LINK_PROVIDERS = 'ProviderLinker';
    const ACTION_IMPACT_VIEW_RECYCLE = 'ImpactViewRecycler';

    // Tabs
    const TABS_FILTER = 'advanced_filter';
    const TABS_CONTENT_OBJECT = 'content_object';
    const TAB_CATEGORY = 'Category';
    const TAB_OBJECT_TYPE = 'ObjectType';
    const TAB_SEARCH = 'Search';
    const TAB_USERVIEW = 'Userview';

    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE_CONTENT_OBJECTS;

    /**
     * Property of this repository manager.
     */
    private $search_parameters;

    private $search_form;

    private $category_menu;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $currentWorkspace;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if($this->getUser() && $this->getRequest()->get(self::PARAM_ACTION) != self::ACTION_DOWNLOAD_DOCUMENT)
        {
            $this->checkAuthorization(Manager::context());
        }

        $this->set_optional_parameters();
    }

    public function set_optional_parameters()
    {
        $this->set_parameter(
            DynamicTabsRenderer::PARAM_SELECTED_TAB,
            Request::get(DynamicTabsRenderer::PARAM_SELECTED_TAB));
    }

    public function has_menu()
    {
        return true;
    }

    public function get_menu()
    {
        $translator = Translation::getInstance();

        $html = array();

        $html[] = '<div id="repository-tree-container">';
        $tabs = new DynamicTabsRenderer(self::TABS_FILTER);

        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_CATEGORY,
                $translator->getTranslation('ViewCategoriesTab', null, Manager::context()),
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self::TAB_CATEGORY),
                $this->get_category_menu()->render_as_tree(),
                DynamicContentTab::DISPLAY_ICON));

        $filter_form = FormFilterRenderer::factory(
            FilterData::getInstance($this->getWorkspace()),
            $this->getWorkspace(),
            $this->get_user_id(),
            $this->get_allowed_content_object_types(),
            $this->get_url(
                array(
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_SEARCH),
                    self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS),
                array(self::PARAM_CATEGORY_ID)));

        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_SEARCH,
                $translator->getTranslation('SearchTab', null, Manager::context()),
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self::TAB_SEARCH),
                $filter_form->render(),
                DynamicContentTab::DISPLAY_ICON));

        $selected_type = FilterData::getInstance($this->getWorkspace())->get_type();
        $selected_category = FilterData::getInstance($this->getWorkspace())->get_type_category();

        $object_type = new ObjectTypeMenu(
            $this,
            $selected_type,
            $this->get_url(
                array(
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_OBJECT_TYPE),
                    FilterData::FILTER_TYPE => '__SELECTION__',
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_OBJECT_TYPE),
                    Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS),
                array(self::PARAM_CATEGORY_ID, self::PARAM_CONTENT_OBJECT_ID)),
            $selected_category,
            $this->get_url(
                array(
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_OBJECT_TYPE),
                    FilterData::FILTER_TYPE => '__CATEGORY__',
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_OBJECT_TYPE),
                    Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS),
                array(self::PARAM_CATEGORY_ID, self::PARAM_CONTENT_OBJECT_ID)));

        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_OBJECT_TYPE,
                $translator->getTranslation('TypeTab', null, Manager::context()),
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self::TAB_OBJECT_TYPE),
                $object_type->render_as_tree(),
                DynamicContentTab::DISPLAY_ICON));

        $current_user_view_id = FilterData::getInstance($this->getWorkspace())->get_user_view();
        $user_view = new UserViewMenu(
            $this,
            $current_user_view_id,
            $this->get_url(
                array(
                    FilterData::FILTER_USER_VIEW => '__VIEW__',
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => array(self::TABS_FILTER => self::TAB_USERVIEW))));
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_USERVIEW,
                $translator->getTranslation('UserViewTab', null, Manager::context()),
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Menu/' . self::TAB_USERVIEW),
                $user_view->render_as_tree(),
                DynamicContentTab::DISPLAY_ICON));

        $html[] = ($tabs->render());

        $html[] = '</div>';

        $html_filter_renderer = HtmlFilterRenderer::factory(
            FilterData::getInstance($this->getWorkspace()),
            $this->getWorkspace());

        $html[] = $html_filter_renderer->render();

        $repositoryMenu = new RepositoryMenu($this);
        $html[] = $repositoryMenu->render();

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
            return array_merge($this->search_parameters, parent::get_parameters());
        }

        return parent::get_parameters();
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
     * Gets the URL to the recycle bin.
     *
     * @return string The URL.
     */
    public function get_recycle_bin_url()
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS, self::PARAM_CATEGORY_ID => null));
    }

    /**
     * Gets the url to view a object.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_viewing_url($content_object)
    {
        if ($content_object->get_state() == ContentObject::STATE_RECYCLED)
        {
            return $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                    self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                    self::PARAM_CATEGORY_ID => null));
        }

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                FilterData::FILTER_CATEGORY => $content_object->get_parent_id()));
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
                self::PARAM_ACTION => self::ACTION_EDIT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to recycle a object (move the object to the recycle bin).
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_recycling_url($content_object, $force = false)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_IMPACT_VIEW_RECYCLE,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to restore a object from the recycle bin.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_restoring_url($content_object)
    {
        if ($content_object->get_state() != ContentObject::STATE_RECYCLED)
        {
            return null;
        }

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_RESTORE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to delete a object from recycle bin.
     *
     * @param $content_object ContentObject The object.
     * @return string The requested URL.
     */
    public function get_content_object_deletion_url($content_object, $type = null)
    {
        if (! \Chamilo\Core\Repository\Storage\DataManager::content_object_deletion_allowed($content_object, $type))
        {
            return null;
        }

        if (isset($type))
        {
            $param = self::PARAM_DELETE_VERSION;
        }
        else
        {
            if ($content_object->get_state() == ContentObject::STATE_RECYCLED)
            {
                $param = self::PARAM_DELETE_PERMANENTLY;
            }
            else
            {
                $param = self::PARAM_DELETE_RECYCLED;
            }
        }

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_DELETE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
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
        if (! \Chamilo\Core\Repository\Storage\DataManager::content_object_revert_allowed($content_object))
        {
            return null;
        }

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_REVERT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
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
                self::PARAM_ACTION => self::ACTION_MOVE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        $types = \Chamilo\Core\Repository\Storage\DataManager::get_registered_types(true);

        foreach ($types as $index => $type)
        {
            $context = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3);

            if (! \Chamilo\Configuration\Configuration::getInstance()->isRegisteredAndActive($context))
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    /**
     * Gets the url for browsing objects of a given type
     *
     * @param int $template_registration_id
     *
     * @return string The url
     */
    public function get_type_filter_url($template_registration_id)
    {
        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
        $params[FilterData::FILTER_TYPE] = $template_registration_id;

        return $this->get_url($params);
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
            DynamicTabsRenderer::PARAM_SELECTED_TAB,
            array(self::TABS_FILTER => self::TAB_CATEGORY));

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

            $this->category_menu = new RepositoryCategoryTreeMenu($this->getWorkspace(), $this);

            if (isset($search_url))
            {
                $this->category_menu->forceCurrentUrl($search_url, true);
            }

            if ($this->get_action() != self::ACTION_BROWSE_CONTENT_OBJECTS)
            {
                $this->category_menu->forceCurrentUrl($this->get_url());
            }
        }

        return $this->category_menu;
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
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE),
            new StaticConditionVariable(ContentObject::STATE_RECYCLED));

        return new AndCondition($conditions);
    }

    /**
     *
     * @return boolean
     */
    public function current_user_has_recycled_objects()
    {
        $parameters = new DataClassCountParameters($this->get_current_user_recycle_bin_conditions());

        return DataManager::count_active_content_objects(ContentObject::class_name(), $parameters) > 0;
    }

    public static function get_document_downloader_url($documentId, $securityCode = null)
    {
        if (! $securityCode)
        {
            $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $documentId);

            if ($contentObject)
            {
                $securityCode = $contentObject->calculate_security_code();
            }
            else
            {
                throw new ObjectNotExistException('ContentObject', $documentId);
            }
        }

        $redirect = new Redirect(
            array(
                self::PARAM_CONTEXT => self::package(),
                self::PARAM_ACTION => self::ACTION_DOWNLOAD_DOCUMENT,
                self::PARAM_CONTENT_OBJECT_ID => $documentId,
                ContentObject::PARAM_SECURITY_CODE => $securityCode));

        return $redirect->getUrl();
    }

    public function get_browse_complex_content_object_url($object)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_BUILD_COMPLEX_CONTENT_OBJECT,
                self::PARAM_CONTENT_OBJECT_ID => $object->get_id()));
    }

    public static function get_preview_content_object_url($content_object)
    {
        return \Chamilo\Core\Repository\Preview\Manager::get_content_object_default_action_link($content_object);
    }

    public function get_content_object_exporting_url($content_object, $type)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_EXPORT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                self::PARAM_EXPORT_TYPE => $type),
            array(self::PARAM_CATEGORY_ID));
    }

    public function get_content_objects_exporting_url($type, $ids, $format)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_EXPORT_CONTENT_OBJECTS,
                $type => $ids,
                self::PARAM_EXPORT_TYPE => $format));
    }

    public function get_publish_content_object_url($content_object)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_PUBLICATION,
                \Chamilo\Core\Repository\Publication\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager::ACTION_PUBLISH,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    public function get_content_object_alternative_linker($content_object)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /*
     * Returns the url of the attachment viewer. Used in rendition implementation @param ContentObject $attachment
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $parameters = $this->get_parameters();
        $parameters[self::PARAM_ATTACHMENT_ID] = $attachment->get_id();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_ATTACHMENT;

        return $this->get_url($parameters);
    }

    public function count_categories($conditions = null)
    {
        return DataManager::count(RepositoryCategory::class_name(), $conditions);
    }

    public function get_copy_content_object_url($content_object_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_COPY_CONTENT_OBJECT,
                self::PARAM_CONTENT_OBJECT_ID => $content_object_id));
    }

    /**
     *
     * @param string $content_object_type
     * @param string $template_registration_id
     *
     * @return string
     */
    public function get_content_object_type_creation_url($template_registration_id)
    {
        return $this->get_url(array(TypeSelector::PARAM_SELECTION => $template_registration_id));
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        if (! isset($this->currentWorkspace))
        {
            $workspaceIdentifier = $this->getRequest()->query->get(self::PARAM_WORKSPACE_ID);

            $workspaceService = new WorkspaceService(new WorkspaceRepository());
            $this->currentWorkspace = $workspaceService->determineWorkspaceForUserByIdentifier(
                $this->get_user(),
                $workspaceIdentifier);
        }

        return $this->currentWorkspace;
    }

    public function get_additional_parameters($additionalParameters = array())
    {
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;

        return $additionalParameters;
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        if($this->get_action() == self::ACTION_HTML_EDITOR_FILE)
        {
            return implode(PHP_EOL, $html);
        }

        if (! $this->getWorkspace() instanceof PersonalWorkspace)
        {
            /** @var Workspace $workspace */
            $workspace = $this->getWorkspace();

            $html[] = '<div class="alert alert-warning" style="font-size: 12px; font-weight: bold;">';
            $html[] = Translation::getInstance()->get(
                'CurrentlyWorkingInWorkspace',
                array('TITLE' => $workspace->getTitle()));

            if ($workspace->getDescription())
            {
                $html[] = '<div style="font-weight: normal; margin-top: 15px; margin-bottom: -10px;">' .
                     $workspace->getDescription() . '</div>';
            }

            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
