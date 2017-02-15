<?php
namespace Chamilo\Core\Repository\External;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

abstract class Manager extends Application implements NoContextComponent
{
    
    // Actions
    const ACTION_VIEW_EXTERNAL_REPOSITORY = 'Viewer';
    const ACTION_EXPORT_EXTERNAL_REPOSITORY = 'Exporter';
    const ACTION_IMPORT_EXTERNAL_REPOSITORY = 'Importer';
    const ACTION_BROWSE_EXTERNAL_REPOSITORY = 'Browser';
    const ACTION_DOWNLOAD_EXTERNAL_REPOSITORY = 'Downloader';
    const ACTION_UPLOAD_EXTERNAL_REPOSITORY = 'Uploader';
    const ACTION_SELECT_EXTERNAL_REPOSITORY = 'Selecter';
    const ACTION_EDIT_EXTERNAL_REPOSITORY = 'Editor';
    const ACTION_DELETE_EXTERNAL_REPOSITORY = 'Deleter';
    const ACTION_CONFIGURE_EXTERNAL_REPOSITORY = 'Configurer';
    const ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY = 'ExternalSyncer';
    const ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY = 'InternalSyncer';
    const ACTION_NEW_FOLDER_EXTERNAL_REPOSITORY = 'NewFolder';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
    
    // Parameters
    const PARAM_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PARAM_EXTERNAL_REPOSITORY = 'external_instance';
    const PARAM_QUERY = 'query';
    const PARAM_RENDERER = 'renderer';
    const PARAM_FOLDER = 'folder';
    const PARAM_USER_QUOTUM = 'default_user_quotum';
    const PARAM_EMBEDDED = 'embedded';

    /**
     *
     * @var ExternalRepository
     */
    private $external_repository;

    private $tabs;

    /**
     *
     * @param $application Application
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        
        $external_instance_id = Request::get(self::PARAM_EXTERNAL_REPOSITORY);
        $this->set_parameter(self::PARAM_EXTERNAL_REPOSITORY, $external_instance_id);
        $this->set_parameter(self::PARAM_EMBEDDED, Request::get(self::PARAM_EMBEDDED, 0));
        
        $this->external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::class_name(), 
            $external_instance_id);
        
        $external_repository_manager_action = Request::get(self::PARAM_ACTION);
        if ($external_repository_manager_action)
        {
            $this->set_parameter(self::PARAM_ACTION, $external_repository_manager_action);
        }
        BreadcrumbTrail::getInstance()->add(new Breadcrumb(null, $this->external_repository->get_title()));
        
        $this->set_optional_parameters();
        if ($this->validate_settings($this->external_repository))
        {
            $this->initialize_external_repository($this);
        }
    }

    /**
     *
     * @return Instance
     */
    public function get_external_repository()
    {
        return $this->external_repository;
    }

    /**
     *
     * @param $external_repository Instance
     */
    public function set_external_repository(Instance $external_repository)
    {
        $this->external_repository = $external_repository;
    }

    /**
     *
     * @return DataConnector
     */
    public function get_external_repository_manager_connector()
    {
        return DataConnector::getInstance($this->get_external_repository());
    }

    public function set_optional_parameters()
    {
        $this->set_parameter(self::PARAM_RENDERER, $this->get_renderer());
    }

    /**
     *
     * @return boolean
     */
    public function is_stand_alone()
    {
        return ! (boolean) $this->get_parameter(self::PARAM_EMBEDDED);
    }

    public function any_object_selected()
    {
    }

    public function render_header()
    {
        $action = $this->get_action();
        
        $html = array();
        
        if (! $this->is_stand_alone())
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        }
        
        $html[] = parent::render_header();
        
        $external_repository_actions = $this->get_external_repository_actions();
        
        if ($action == self::ACTION_EDIT_EXTERNAL_REPOSITORY)
        {
            $external_repository_actions[] = self::ACTION_EDIT_EXTERNAL_REPOSITORY;
        }
        
        if ($action == self::ACTION_VIEW_EXTERNAL_REPOSITORY)
        {
            $external_repository_actions[] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
        }
        
        $this->tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities::getInstance()->getClassnameFromObject($this, true));
        
        foreach ($external_repository_actions as $external_repository_action)
        {
            if ($action == $external_repository_action)
            {
                $selected = true;
            }
            else
            {
                $selected = false;
            }
            
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = $external_repository_action;
            
            if ($external_repository_action == self::ACTION_VIEW_EXTERNAL_REPOSITORY)
            {
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = Request::get(self::PARAM_EXTERNAL_REPOSITORY_ID);
            }
            
            $label = htmlentities(
                Translation::get(
                    (string) StringUtilities::getInstance()->createString($external_repository_action)->upperCamelize() .
                         'Title'));
            $link = $this->get_url($parameters);
            
            $icon = 'Place/Tab/' . $external_repository_action;
            $icon_path = Theme::getInstance()->getImagePath($this->get_external_repository()->get_type(), $icon);
            $icon_system_path = Theme::getInstance()->getImagePath(
                $this->get_external_repository()->get_type(), 
                $icon, 
                'png', 
                false);
            
            if (! file_exists($icon_system_path))
            {
                $icon_path = Theme::getInstance()->getCommonImagePath($icon);
            }
            
            $this->tabs->add_tab(
                new DynamicVisualTab($external_repository_action, $label, $icon_path, $link, $selected));
        }
        
        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return array
     */
    public function get_external_repository_actions()
    {
        $actions = array();
        $actions[] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $actions[] = self::ACTION_UPLOAD_EXTERNAL_REPOSITORY;
        
        $is_platform = $this->get_user()->is_platform_admin();
        $has_setting = $this->get_external_repository()->has_settings();
        $has_user_setting = $this->get_external_repository()->has_user_settings();
        
        if (! ((! $has_setting) || (! $has_user_setting && ! $is_platform)))
        {
            $actions[] = self::ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }
        
        return $actions;
    }

    /**
     *
     * @return multitype:string
     */
    public function get_instance_identifier()
    {
        return array();
    }

    public function render_footer()
    {
        $html = array();
        
        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();
        $html[] = parent::render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public static function get_object_viewing_parameters($external_instance_sync)
    {
        return array(
            self::PARAM_ACTION => self::ACTION_VIEW_EXTERNAL_REPOSITORY, 
            self::PARAM_EXTERNAL_REPOSITORY_ID => $external_instance_sync->get_external_object_id());
    }

    /**
     *
     * @param $condition mixed
     */
    public function count_external_repository_objects($condition)
    {
        return $this->get_external_repository_manager_connector()->count_external_repository_objects($condition);
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     * @return ArrayResultSet
     */
    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_external_repository_manager_connector()->retrieve_external_repository_objects(
            $condition, 
            $order_property, 
            $offset, 
            $count);
    }

    /**
     *
     * @param $external_repository_manager Manager
     */
    public function initialize_external_repository(Manager $external_repository_manager)
    {
        $this->get_external_repository_manager_connector();
    }

    /**
     *
     * @return boolean
     */
    abstract public function validate_settings($external_repository);

    /**
     *
     * @return string
     */
    public function support_sorting_direction()
    {
        return true;
    }

    /**
     *
     * @param $query mixed
     */
    public function translate_search_query($query)
    {
        return $this->get_external_repository_manager_connector()->translate_search_query($query);
    }

    /**
     *
     * @return array
     */
    abstract public function get_menu_items();

    public function get_menu()
    {
        return null;
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     * @return string
     */
    abstract public function get_external_repository_object_viewing_url($object);

    /**
     *
     * @param $id string
     * @return ExternalObject
     */
    public function retrieve_external_repository_object($id)
    {
        return $this->get_external_repository_manager_connector()->retrieve_external_repository_object($id);
    }

    /**
     *
     * @param $id string
     * @return boolean
     */
    public function delete_external_repository_object($id)
    {
        return $this->get_external_repository_manager_connector()->delete_external_repository_object($id);
    }

    /**
     *
     * @param $id string
     * @return boolean
     */
    public function export_external_repository_object($id)
    {
        return $this->get_external_repository_manager_connector()->export_external_repository_object($id);
    }

    /**
     *
     * @param $object ExternalObject
     * @return array
     */
    public function get_external_repository_object_actions(ExternalObject $object)
    {
        $toolbar_items = array();
        
        if ($object->is_editable())
        {
            $toolbar_items[self::ACTION_EDIT_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_EDIT_EXTERNAL_REPOSITORY, 
                        self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                ToolbarItem::DISPLAY_ICON);
        }
        
        if ($object->is_deletable())
        {
            $toolbar_items[self::ACTION_DELETE_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_DELETE_EXTERNAL_REPOSITORY, 
                        self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                ToolbarItem::DISPLAY_ICON);
        }
        
        if ($object->is_usable())
        {
            if (! $this->is_stand_alone())
            {
                $toolbar_items[] = new ToolbarItem(
                    Translation::get('Select', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Publish'), 
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_SELECT_EXTERNAL_REPOSITORY, 
                            self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                    ToolbarItem::DISPLAY_ICON);
            }
            else
            {
                if ($object->is_importable())
                {
                    $toolbar_items[self::ACTION_IMPORT_EXTERNAL_REPOSITORY] = new ToolbarItem(
                        Translation::get('Import', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Import'), 
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_IMPORT_EXTERNAL_REPOSITORY, 
                                self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                        ToolbarItem::DISPLAY_ICON);
                }
                else
                {
                    switch ($object->get_synchronization_status())
                    {
                        case \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData::SYNC_STATUS_INTERNAL :
                            $toolbar_items[self::ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY] = new ToolbarItem(
                                Translation::get('UpdateRepositoryObject'), 
                                Theme::getInstance()->getCommonImagePath('Action/Synchronize'), 
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY, 
                                        self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                                ToolbarItem::DISPLAY_ICON);
                            break;
                        case \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData::SYNC_STATUS_EXTERNAL :
                            if ($object->is_editable())
                            {
                                $toolbar_items[self::ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY] = new ToolbarItem(
                                    Translation::get(
                                        'UpdateExternalObject', 
                                        array('TYPE' => $this->get_external_repository()->get_title())), 
                                    Theme::getInstance()->getImagePath($object::context(), 'Logo/16'), 
                                    $this->get_url(
                                        array(
                                            self::PARAM_ACTION => self::ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY, 
                                            self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                                    ToolbarItem::DISPLAY_ICON);
                            }
                            break;
                        case \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData::SYNC_STATUS_CONFLICT :
                            $toolbar_items[self::ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY] = new ToolbarItem(
                                Translation::get('UpdateRepositoryObject'), 
                                Theme::getInstance()->getCommonImagePath('Action/Synchronize'), 
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY, 
                                        self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                                ToolbarItem::DISPLAY_ICON);
                            if ($object->is_editable())
                            {
                                $toolbar_items[self::ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY] = new ToolbarItem(
                                    Translation::get(
                                        'UpdateExternalObject', 
                                        array('TYPE' => $this->get_external_repository()->get_name())), 
                                    Theme::getInstance()->getImagePath($object::context(), 'Logo/16'), 
                                    $this->get_url(
                                        array(
                                            self::PARAM_ACTION => self::ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY, 
                                            self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), 
                                    ToolbarItem::DISPLAY_ICON);
                            }
                            break;
                    }
                }
            }
        }
        return $toolbar_items;
    }

    /**
     *
     * @return string
     */
    public function get_renderer()
    {
        $renderer = Request::get(self::PARAM_RENDERER);
        
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

    /**
     *
     * @return array
     */
    public function get_available_renderers()
    {
        return array(Renderer::TYPE_TABLE);
    }

    /**
     *
     * @return Condition
     */
    abstract public function get_content_object_type_conditions();

    /**
     *
     * @param $type string
     * @return boolean
     */
    public static function exists($type)
    {
        $path = Path::getInstance()->namespaceToFullPath(__NAMESPACE__) . '../implementation';
        $external_repository_path = $path . '/' . $type;
        $external_repository_manager_path = $external_repository_path . '/php/' . $type .
             '_external_repository_manager.class.php';
        
        if (file_exists($external_repository_path) && is_dir($external_repository_path) && file_exists(
            $external_repository_manager_path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function get_namespace($type = null)
    {
        if(empty($type))
        {
            return __NAMESPACE__;
        }

        if (strpos($type, '\\') === false)
        {
            return 'Chamilo\Core\Repository\Implementation\\' . $type;
        }

        return $type;
    }

    public static function get_registered_types($status = Registration :: STATUS_ACTIVE)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration::class_name(), Registration::PROPERTY_TYPE), 
            new StaticConditionVariable('Chamilo\Core\Repository\Implementation'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration::class_name(), Registration::PROPERTY_STATUS), 
            new StaticConditionVariable($status));
        $condition = new AndCondition($conditions);
        
        return \Chamilo\Core\Repository\Storage\DataManager::retrieves(
            Registration::class_name(), 
            new DataClassRetrievesParameters($condition));
    }

    public static function get_repository_menu_parameter()
    {
        return array(self::PARAM_ACTION => self::ACTION_BROWSE_EXTERNAL_REPOSITORY);
    }

    public static function get_packages_from_filesystem()
    {
        $external_repository_managers = array();
        
        $path = Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\Implementation');
        $directories = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);
        
        foreach ($directories as $directory)
        {
            $namespace = self::get_namespace(basename($directory));
            
            if (\Chamilo\Configuration\Package\Storage\DataClass\Package::exists($namespace))
            {
                $external_repository_managers[] = $namespace;
            }
        }
        
        return $external_repository_managers;
    }

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\External\Action\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
