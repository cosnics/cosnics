<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Group\UserInterface\Menu
 * @author Bart Mollet
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMenu extends HtmlMenu
{
    public const TREE_NAME = __CLASS__;

    private HtmlMenuArrayRenderer $arrayRenderer;

    private Group $currentGroup;

    private bool $hideCurrentCategory;

    private bool $includeRoot;

    private bool $showCompleteTree;

    private string $urlFormat;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function __construct(
        Group $currentGroup, string $urlFormat, bool $includeRoot = true, bool $showCompleteTree = false,
        bool $hideCurrentCategory = false
    )
    {
        $this->includeRoot = $includeRoot;
        $this->showCompleteTree = $showCompleteTree;
        $this->hideCurrentCategory = $hideCurrentCategory;
        $this->currentGroup = $currentGroup;
        $this->urlFormat = $urlFormat;
        $menu = $this->getMenu();

        parent::__construct($menu);

        $this->arrayRenderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->getUrl($this->currentGroup->getId()));
    }

    public function getBreadcrumbs(): array
    {
        $this->render($this->arrayRenderer, 'urhere');

        $breadcrumbs = $this->arrayRenderer->toArray();

        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }

        return $breadcrumbs;
    }

    private function getHomeUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_BROWSE_GROUPS]
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getMenu(): array
    {
        $include_root = $this->includeRoot;

        $group = $this->getGroupService()->findRootGroup();

        if (!$include_root)
        {
            return $this->getMenuItems($group->getId());
        }
        else
        {
            $menu = [];

            $menuItem = [];
            $menuItem['title'] = $group->getName();
            $menuItem['url'] = $this->getHomeUrl();

            $subMenuItems = $this->getMenuItems($group->getId());

            if (count($subMenuItems) > 0)
            {
                $menuItem['sub'] = $subMenuItems;
            }

            $glyph = new FontAwesomeGlyph('home', [], null, 'fas');

            $menuItem['class'] = $glyph->getClassNamesString();
            $menuItem[OptionsMenuRenderer::KEY_ID] = $group->getId();

            $menu[$group->getId()] = $menuItem;

            return $menu;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    private function getMenuItems($parent_id = 0): array
    {
        $currentGroup = $this->currentGroup;

        $showCompleteTree = $this->showCompleteTree;
        $hideCurrentCategory = $this->hideCurrentCategory;

        $groups = $this->getGroupService()->findGroupsForParentIdentifier($parent_id);

        $menu = [];

        foreach ($groups as $group)
        {
            if (!($group->getId() == $currentGroup->getId() && $hideCurrentCategory))
            {
                $menuItem = [];

                $menuItem['title'] = $group->getName();
                $menuItem['url'] = $this->getUrl($group->getId());

                if ($group->isAncestorOf($currentGroup) || $group->getId() == $currentGroup->getId() ||
                    $showCompleteTree)
                {
                    if ($group->hasChildren())
                    {
                        $menuItem['sub'] = $this->getMenuItems($group->getId());
                    }
                }
                elseif ($group->hasChildren())
                {
                    $menuItem['children'] = 'expand';
                }

                $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

                $menuItem['class'] = $glyph->getClassNamesString();
                $menuItem[OptionsMenuRenderer::KEY_ID] = $group->getId();

                $menu[$group->getId()] = $menuItem;
            }
        }

        return $menu;
    }

    public function getUrl($group): string
    {
        return htmlentities(sprintf($this->urlFormat, $group));
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    public function renderAsTree(): string
    {
        $feedUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_XML_GROUP_MENU_FEED
            ]
        );

        $renderer = new TreeMenuRenderer('group_menu', $feedUrl, $this->urlFormat);
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
