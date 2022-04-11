<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Core\Admin\Form\AdminSearchForm;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeLinksMenu;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Tabs\DynamicActionsTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;

class BrowserComponent extends Manager implements DelegateComponent
{
    const PARAM_TAB = 'tab';

    private $currentTab;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->get_tabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function getCurrentTab()
    {
        if (!isset($this->currentTab))
        {
            $this->currentTab = $this->getRequest()->query->get(
                self::PARAM_TAB, ClassnameUtilities::getInstance()->getNamespaceId('Chamilo\Core')
            );
        }

        return $this->currentTab;
    }

    /**
     * @return string
     */
    public function get_menu()
    {
        $tabNamespace = ClassnameUtilities::getInstance()->getNamespaceFromId($this->getCurrentTab());
        $menu = new PackageTypeLinksMenu($tabNamespace, $this->get_url(array(self::PARAM_TAB => '__TYPE__')));

        return $menu->render_as_tree();
    }

    /**
     * @return string
     */
    public function get_tabs()
    {
        $tabs = new DynamicTabsRenderer('admin');

        $packages = PlatformPackageBundles::getInstance(PackageList::MODE_INSTALLED)->get_type_packages();

        $packageNames = [];

        $tabNamespace = ClassnameUtilities::getInstance()->getNamespaceFromId($this->getCurrentTab());

        foreach ($packages[$tabNamespace] as $namespace => $package)
        {
            $packageNames[$this->getTranslator()->trans('TypeName', [], $namespace)] = $package;
        }

        ksort($packageNames);

        foreach ($packageNames as $packageName => $package)
        {
            $managerClass = $package->get_context() . '\Integration\Chamilo\Core\Admin\Manager';



            if (class_exists($managerClass) &&
                is_subclass_of($managerClass, '\Chamilo\Core\Admin\ActionsSupportInterface'))
            {
                $links = $managerClass::get_actions();

                $index = 0;
                $index ++;

                $actions_tab = new DynamicActionsTab(
                    ClassnameUtilities::getInstance()->getNamespaceId($package->get_context()),
                    $this->getTranslator()->trans('TypeName', [], $package->get_context()),
                    new NamespaceIdentGlyph(
                        $package->get_context(), true, false, false,
                        IdentGlyph::SIZE_SMALL
                    )
                );

                if ($links->get_search())
                {
                    $search_form = new AdminSearchForm($links->get_search(), $index);
                    $actions_tab->add_action(
                        new DynamicAction(
                            null, $search_form->render(), new FontAwesomeGlyph(
                                'search', array('fa-fw', 'fa-2x'), null, 'fas'
                            )
                        )
                    );
                }

                foreach ($links->get_links() as $action)
                {
                    $actions_tab->add_action($action);
                }

                $tabs->add_tab($actions_tab);
            }
        }

        return $tabs->render();
    }

    /**
     * @return boolean
     */
    public function has_menu()
    {
        return true;
    }
}
