<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Core\Lynx\Menu\PackageTypeMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\MenuComponentInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
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
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface, MenuComponentInterface
{

    public const STATUS_AVAILABLE = 2;
    public const STATUS_INSTALLED = 1;

    private string $currentType;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->add_admin_breadcrumb();

        $this->set_parameter(Manager::PARAM_REGISTRATION_TYPE, $this->getCurrentType());

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->get_content();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_admin_breadcrumb(): void
    {
        $breadcrumb_trail = $this->getBreadcrumbTrail();
        $breadcrumbs = $breadcrumb_trail->getBreadcrumbs();

        $adminUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT]
        );

        array_splice(
            $breadcrumbs, 1, 0,
            [new Breadcrumb($adminUrl, $this->getTranslator()->trans('Administration', [], Manager::CONTEXT))]
        );

        $breadcrumb_trail->set($breadcrumbs);
    }

    public function getArrayCollectionTableRenderer(): ArrayCollectionTableRenderer
    {
        return $this->getService(ArrayCollectionTableRenderer::class);
    }

    public function getCurrentType(): string
    {
        if (!isset($this->currentType))
        {
            $this->currentType =
                $this->getRequest()->query->get(Manager::PARAM_REGISTRATION_TYPE, 'Chamilo\Application');
        }

        return $this->currentType;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \TableException
     */
    public function get_available_packages_table(): string
    {
        $translator = $this->getTranslator();
        $packages = $this->getPackageBundlesCacheService()->getAvailablePackages()->getNestedTypedPackages();

        $table_data = [];

        foreach ($packages[$this->currentType] as $package_info)
        {
            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewPackageDetails', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('desktop', [], null, 'fas'), $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_VIEW,
                        self::PARAM_CONTEXT => $package_info->get_context()
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Install', [], Manager::CONTEXT), new FontAwesomeGlyph('box', [], null, 'fas'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_INSTALL,
                            self::PARAM_CONTEXT => $package_info->get_context()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $row = [];
            $row[] = $translator->trans('TypeName', [], $package_info->get_context());
            $row[] = $toolbar->render();

            $table_data[] = $row;
        }

        $headers = [];
        $headers[] = new SortableStaticTableColumn('package', $translator->trans('Package', [], Manager::CONTEXT));
        $headers[] = new StaticTableColumn('', '');

        return $this->getArrayCollectionTableRenderer()->render(
            $headers, new ArrayCollection($table_data), 0, SORT_ASC, 200, 'available_packages'
        );
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function get_content(): string
    {
        $translator = $this->getTranslator();
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
                    (string) self::STATUS_INSTALLED, $translator->trans('InstalledPackages', [], Manager::CONTEXT),
                    $this->get_registered_packages_table(),
                    new FontAwesomeGlyph('check-circle', ['fa-lg', 'fas-ci-va', 'text-success'], null, 'fas')
                )
            );
        }

        $packages = $this->getPackageBundlesCacheService()->getAvailablePackages()->getNestedTypedPackages();

        if (count($packages[$this->getCurrentType()]) > 0)
        {
            $tabs->add(
                new ContentTab(
                    (string) self::STATUS_AVAILABLE, $translator->trans('AvailablePackages', [], Manager::CONTEXT),
                    $this->get_available_packages_table(),
                    new FontAwesomeGlyph('box', ['fa-lg', 'fas-ci-va'], null, 'fas')
                )
            );
        }

        if ($tabs->count() > 0)
        {
            return $this->getTabsRenderer()->render(
                $this->getClassnameUtilities()->getClassnameFromObject($this, true), $tabs
            );
        }
        else
        {
            return Display::normal_message(
                $translator->trans('NoPackagesAvailableInContextGetSomeNow', [], Manager::CONTEXT)
            );
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderApplicationMenu(): string
    {
        $menu = new PackageTypeMenu(
            $this->getPackageBundlesCacheService(), $this->getClassnameUtilities(), $this->getCurrentType(),
            $this->get_url([Manager::PARAM_REGISTRATION_TYPE => '__type__'])
        );

        return $menu->render_as_tree();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function get_registered_packages_table(): string
    {
        $translator = $this->getTranslator();

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
                    $translator->trans('ViewPackageDetails', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('desktop', [], null, 'fas'), $this->get_url(
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
                            $translator->trans('Deactivate', [], StringUtilities::LIBRARIES),
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
            elseif (!is_subclass_of(
                $registration->get_context() . '\Activator', 'Chamilo\Configuration\Package\NotAllowed'
            ))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Activate', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('play-circle', [], null, 'fas'), $this->get_url(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE,
                            Manager::PARAM_CONTEXT => $registration->get_context()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if (!is_subclass_of($registration->get_context() . '\Remover', 'Chamilo\Configuration\Package\NotAllowed'))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Remove', [], StringUtilities::LIBRARIES),
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
            $row[] = $translator->trans('TypeName', [], $registration->get_context());
            $row[] = $toolbar->render();

            $table_data[] = $row;
        }

        $headers = [];
        $headers[] = new SortableStaticTableColumn('package', $translator->trans('Package', [], Manager::CONTEXT));
        $headers[] = new StaticTableColumn('', '');

        return $this->getArrayCollectionTableRenderer()->render(
            $headers, new ArrayCollection($table_data), 0, SORT_ASC, 200, 'registered_packages'
        );
    }
}
