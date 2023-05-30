<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Core\Lynx\Menu\PackageTypeMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\ArrayCollectionTableRenderer;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    public const STATUS_AVAILABLE = 2;
    public const STATUS_INSTALLED = 1;

    /**
     * @var string
     */
    private $current_type;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->add_admin_breadcrumb();

        $this->set_parameter(Manager::PARAM_REGISTRATION_TYPE, $this->getCurrentType());

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->get_content();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_admin_breadcrumb()
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();

        $adminUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT]
        );

        array_splice(
            $breadcrumbs, 1, 0, [new Breadcrumb($adminUrl, Translation::get('Administration'))]
        );

        $breadcrumb_trail->set($breadcrumbs);
    }

    /**
     * @return string
     */
    public function getCurrentType()
    {
        if (!isset($this->current_type))
        {
            $this->current_type =
                $this->getRequest()->query->get(Manager::PARAM_REGISTRATION_TYPE, 'Chamilo\Application');
        }

        return $this->current_type;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function get_available_packages_table()
    {
        $packages = PlatformPackageBundles::getInstance(
            PlatformPackageBundles::MODE_AVAILABLE
        )->get_type_packages();

        $table_data = [];

        foreach ($packages[$this->current_type] as $package_info)
        {
            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewPackageDetails'), new FontAwesomeGlyph('desktop', [], null, 'fas'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_VIEW,
                            self::PARAM_CONTEXT => $package_info->get_context()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Install'), new FontAwesomeGlyph('box', [], null, 'fas'), $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_INSTALL,
                        self::PARAM_CONTEXT => $package_info->get_context()
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );

            $row = [];
            $row[] = Translation::get('TypeName', null, $package_info->get_context());
            $row[] = $toolbar->as_html();

            $table_data[] = $row;
        }

        $headers = [];
        $headers[] = new SortableStaticTableColumn(Translation::get('Package'));
        $headers[] = new StaticTableColumn('');

        $table = new ArrayCollectionTableRenderer(
            $table_data, $headers, $this->get_parameters(), 0, 20, SORT_ASC, 'available_packages'
        );

        return $table->toHtml();
    }

    public function get_content()
    {
        $tabs = new TabsCollection();

        $count = DataManager::count(
            Registration::class, new DataClassCountParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Registration::class, Registration::PROPERTY_TYPE),
                    new StaticConditionVariable($this->getCurrentType())
                )
            )
        );

        if ($count > 0)
        {
            $tabs->add(
                new ContentTab(
                    self::STATUS_INSTALLED, Translation::get('InstalledPackages'),
                    $this->get_registered_packages_table(),
                    new FontAwesomeGlyph('check-circle', ['fa-lg', 'fas-ci-va', 'text-success'], null, 'fas')
                )
            );
        }

        $packages = PlatformPackageBundles::getInstance(
            PlatformPackageBundles::MODE_AVAILABLE
        )->get_type_packages();

        if (count($packages[$this->getCurrentType()]) > 0)
        {
            $tabs->add(
                new ContentTab(
                    self::STATUS_AVAILABLE, Translation::get('AvailablePackages'),
                    $this->get_available_packages_table(),
                    new FontAwesomeGlyph('box', ['fa-lg', 'fas-ci-va'], null, 'fas')
                )
            );
        }

        if ($tabs->count() > 0)
        {
            return $this->getTabsRenderer()->render(
                ClassnameUtilities::getInstance()->getClassnameFromObject($this, true), $tabs
            );
        }
        else
        {
            return Display::normal_message(Translation::get('NoPackagesAvailableInContextGetSomeNow'), true);
        }
    }

    public function get_menu(): string
    {
        $menu = new PackageTypeMenu(
            $this->getCurrentType(), $this->get_url([Manager::PARAM_REGISTRATION_TYPE => '__type__'])
        );

        return $menu->render_as_tree();
    }

    public function get_registered_packages_table()
    {
        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Registration::class, Registration::PROPERTY_TYPE),
                new StaticConditionVariable($this->getCurrentType())
            )
        );
        $registrations = DataManager::retrieves(Registration::class, $parameters);

        $table_data = [];

        foreach ($registrations as $registration)
        {
            $toolbar = new Toolbar();

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewPackageDetails'), new FontAwesomeGlyph('desktop', [], null, 'fas'),
                    $this->get_url(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_VIEW,
                            Manager::PARAM_CONTEXT => $registration->get_context()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            if ($registration->is_active())
            {
                if (!is_subclass_of(
                    $registration->get_context() . '\Deactivator', 'Chamilo\Configuration\Package\NotAllowed'
                ))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('Deactivate', [], StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('pause-circle', [], null, 'fas'), $this->get_url(
                            [
                                Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE,
                                Manager::PARAM_CONTEXT => $registration->get_context()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
            else
            {
                if (!is_subclass_of(
                    $registration->get_context() . '\Activator', 'Chamilo\Configuration\Package\NotAllowed'
                ))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('Activate', [], StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('play-circle', [], null, 'fas'), $this->get_url(
                            [
                                Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE,
                                Manager::PARAM_CONTEXT => $registration->get_context()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }

            if (!is_subclass_of($registration->get_context() . '\Remover', 'Chamilo\Configuration\Package\NotAllowed'))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Remove', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('trash-alt', [], null, 'fas'), $this->get_url(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_REMOVE,
                            Manager::PARAM_CONTEXT => $registration->get_context()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            $row = [];
            $row[] = Translation::get('TypeName', null, $registration->get_context());
            $row[] = $toolbar->as_html();

            $table_data[] = $row;
        }

        $headers = [];
        $headers[] = new SortableStaticTableColumn(Translation::get('Package'));
        $headers[] = new StaticTableColumn('');

        $table = new ArrayCollectionTableRenderer(
            $table_data, $headers, $this->get_parameters(), 0, 20, SORT_ASC, 'registered_packages'
        );

        return $table->toHtml();
    }

    public function has_menu(): bool
    {
        return true;
    }
}
