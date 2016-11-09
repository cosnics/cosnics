<?php
namespace Chamilo\Core\Lynx\Manager\Component;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Lynx\Manager\Manager;
use Chamilo\Core\Lynx\Manager\Menu\PackageTypeMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $current_type;
    const STATUS_INSTALLED = 1;
    const STATUS_AVAILABLE = 2;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->add_admin_breadcrumb();
        
        $this->current_type = Request :: get(Manager :: PARAM_REGISTRATION_TYPE);
        $this->current_type = $this->current_type ? $this->current_type : 'application';
        
        $this->set_parameter(Manager :: PARAM_REGISTRATION_TYPE, $this->current_type);
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $menu = new PackageTypeMenu(
            $this->current_type, 
            $this->get_url(array(Manager :: PARAM_REGISTRATION_TYPE => '__type__')));
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = '<div style="float: left; width: 17%;">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 82%;">';
        $html[] = $this->get_content();
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_content()
    {
        $tabs = new DynamicTabsRenderer(ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true));
        
        $count = DataManager :: count(
            Registration :: class_name(), 
            new DataClassCountParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_TYPE), 
                    new StaticConditionVariable($this->get_type()))));
        
        if ($count > 0)
        {
            $tabs->add_tab(
                new DynamicContentTab(
                    self :: STATUS_INSTALLED, 
                    Translation :: get('InstalledPackages'), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Status/' . self :: STATUS_INSTALLED), 
                    $this->get_registered_packages_table()));
        }
        
        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance(
            \Chamilo\Configuration\Package\PlatformPackageBundles :: MODE_AVAILABLE)->get_type_packages();
        
        if (count($packages[$this->get_type()]) > 0)
        {
            $tabs->add_tab(
                new DynamicContentTab(
                    self :: STATUS_AVAILABLE, 
                    Translation :: get('AvailablePackages'), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Status/' . self :: STATUS_AVAILABLE), 
                    $this->get_available_packages_table()));
        }
        
        if ($tabs->size() > 0)
        {
            return $tabs->render();
        }
        else
        {
            return Display :: normal_message(Translation :: get('NoPackagesAvailableInContextGetSomeNow'), true);
        }
    }

    public function get_registered_packages_table()
    {
        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_TYPE), 
                new StaticConditionVariable($this->get_type())));
        $registrations = DataManager :: retrieves(Registration :: class_name(), $parameters);
        
        $table_data = array();
        
        while ($registration = $registrations->next_result())
        {
            $toolbar = new Toolbar();
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewPackageDetails'), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/View'), 
                    $this->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_VIEW, 
                            Manager :: PARAM_CONTEXT => $registration->get_context())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            if ($registration->is_active())
            {
                if (! is_subclass_of(
                    $registration->get_context() . '\Deactivator', 
                    'Chamilo\Configuration\Package\NotAllowed'))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES), 
                            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Deactivate'), 
                            $this->get_url(
                                array(
                                    Manager :: PARAM_ACTION => Manager :: ACTION_DEACTIVATE, 
                                    Manager :: PARAM_CONTEXT => $registration->get_context())), 
                            ToolbarItem :: DISPLAY_ICON));
                }
                
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('UpgradeNotAllowed', array(), Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/UpgradeNa'), 
                        null, 
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                if (! is_subclass_of(
                    $registration->get_context() . '\Activator', 
                    'Chamilo\Configuration\Package\NotAllowed'))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES), 
                            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Activate'), 
                            $this->get_url(
                                array(
                                    Manager :: PARAM_ACTION => Manager :: ACTION_ACTIVATE, 
                                    Manager :: PARAM_CONTEXT => $registration->get_context())), 
                            ToolbarItem :: DISPLAY_ICON));
                }
                
                $package = Package :: get($registration->get_context());
                
                if ($package && version_compare($package->get_version(), $registration->get_version(), '>'))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Upgrade', array(), Utilities :: COMMON_LIBRARIES), 
                            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Upgrade'), 
                            $this->get_url(
                                array(
                                    Manager :: PARAM_ACTION => Manager :: ACTION_UPGRADE, 
                                    Manager :: PARAM_CONTEXT => $registration->get_context())), 
                            ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('UpgradeNotAllowed', array(), Utilities :: COMMON_LIBRARIES), 
                            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/UpgradeNa'), 
                            null, 
                            ToolbarItem :: DISPLAY_ICON));
                }
            }
            
            if (! is_subclass_of($registration->get_context() . '\Remover', 'Chamilo\Configuration\Package\NotAllowed'))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Remove', array(), Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Remove'), 
                        $this->get_url(
                            array(
                                Manager :: PARAM_ACTION => Manager :: ACTION_REMOVE, 
                                Manager :: PARAM_CONTEXT => $registration->get_context())), 
                        ToolbarItem :: DISPLAY_ICON));
            }
            
            $row = array();
            $row[] = Translation :: get('TypeName', null, $registration->get_context());
            $row[] = $toolbar->as_html();
            
            $table_data[] = $row;
        }
        
        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation :: get('Package'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray(
            $table_data, 
            $headers, 
            $this->get_parameters(), 
            0, 
            20, 
            SORT_ASC, 
            'registered_packages');
        
        return $table->toHtml();
    }

    public function get_available_packages_table()
    {
        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance(
            \Chamilo\Configuration\Package\PlatformPackageBundles :: MODE_AVAILABLE)->get_type_packages();
        
        $table_data = array();
        
        foreach ($packages[$this->current_type] as $package_info)
        {
            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewPackageDetails'), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/View'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_VIEW, 
                            self :: PARAM_CONTEXT => $package_info->get_context())), 
                    ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Install'), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Install'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_INSTALL, 
                            self :: PARAM_CONTEXT => $package_info->get_context())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $row = array();
            $row[] = Translation :: get('TypeName', null, $package_info->get_context());
            $row[] = $toolbar->as_html();
            
            $table_data[] = $row;
        }
        
        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation :: get('Package'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray(
            $table_data, 
            $headers, 
            $this->get_parameters(), 
            0, 
            20, 
            SORT_ASC, 
            'available_packages');
        
        return $table->toHtml();
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation :: get('ManageSources'), 
                    Theme :: getInstance()->getImagePath(\Chamilo\Core\Lynx\Source\Manager :: context(), 'Logo/16'), 
                    $this->get_url(
                        array(
                            \Chamilo\Core\Lynx\Manager :: PARAM_ACTION => \Chamilo\Core\Lynx\Manager :: ACTION_SOURCE, 
                            Manager :: PARAM_ACTION => null)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $commonActions->addButton(
                new Button(
                    Translation :: get('BrowseRemote'), 
                    Theme :: getInstance()->getImagePath(\Chamilo\Core\Lynx\Remote\Manager :: context(), 'Logo/16'), 
                    $this->get_url(
                        array(
                            \Chamilo\Core\Lynx\Manager :: PARAM_ACTION => \Chamilo\Core\Lynx\Manager :: ACTION_REMOTE, 
                            Manager :: PARAM_ACTION => null)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    public function get_type()
    {
        return $this->current_type;
    }

    public function add_admin_breadcrumb()
    {
        $breadcrumb_trail = BreadcrumbTrail :: getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        
        $redirect = new Redirect(array(Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context()));
        
        array_splice(
            $breadcrumbs, 
            1, 
            0, 
            array(new Breadcrumb($redirect->getUrl(), Translation :: get('Administration'))));
        $breadcrumb_trail->set($breadcrumbs);
    }
}
