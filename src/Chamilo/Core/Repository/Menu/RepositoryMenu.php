<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Menu;
use HTML_Menu_ArrayRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

class RepositoryMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     *
     * @var Manager
     */
    private $repository_manager;

    public function __construct($repository_manager, $current_category = null, $url_format = '?category=%s')
    {
        $this->urlFmt = $url_format;
        $this->repository_manager = $repository_manager;
        parent :: __construct($this->get_menu_items());
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_category_url($current_category));
    }

    private function get_menu_items()
    {
        $extra_items = array();

        if ($this->repository_manager->getWorkspace() instanceof PersonalWorkspace)
        {
            $pub = array();
            $pub['title'] = Translation :: get('MyPublications');
            $pub['url'] = $this->repository_manager->get_url(
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_PUBLICATION),
                array(\Chamilo\Core\Repository\Publication\Manager :: PARAM_ACTION),
                false);
            $pub['class'] = 'publication';

            $extra_items[] = $pub;
        }

        if (! $this->repository_manager->getWorkspace() instanceof PersonalWorkspace)
        {
            $add = array();
            $add['title'] = Translation :: get('AddExisting', null, Utilities :: COMMON_LIBRARIES);
            $add['url'] = $this->repository_manager->get_url(
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_WORKSPACE,
                    \Chamilo\Core\Repository\Workspace\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager :: ACTION_PUBLISH));
            $add['class'] = 'add';

            $extra_items[] = $add;
        }

        $create = array();
        $create['title'] = Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES);
        $create['url'] = $this->repository_manager->get_url(
            array(
                \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_CREATE_CONTENT_OBJECTS));
        $create['class'] = 'create';

        $extra_items[] = $create;

        if ($this->repository_manager->getWorkspace() instanceof PersonalWorkspace)
        {
            $import = array();
            $import['title'] = Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES);
            $import['url'] = $this->repository_manager->get_url(
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_IMPORT_CONTENT_OBJECTS));
            $import['class'] = 'import';

            $extra_items[] = $import;

            $templates = array();
            $templates['title'] = Translation :: get('BrowseTemplates');
            $templates['url'] = $this->repository_manager->get_url(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_TEMPLATE));
            $templates['class'] = 'template';

            $extra_items[] = $templates;

            $quota = array();
            $quota['title'] = Translation :: get('Quota');
            $quota['url'] = $this->repository_manager->get_url(
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_QUOTA,
                    \Chamilo\Core\Repository\Manager :: PARAM_CATEGORY_ID => null,
                    \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => null));
            $quota['class'] = 'quota';

            $extra_items[] = $quota;

            $doubles = array();
            $doubles['title'] = Translation :: get('ViewDoubles');
            $doubles['url'] = $this->repository_manager->get_url(
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_VIEW_DOUBLES));
            $doubles['class'] = 'doubles';

            $extra_items[] = $doubles;

            $trash = array();
            $trash['title'] = Translation :: get('RecycleBin');
            $trash['url'] = $this->repository_manager->get_recycle_bin_url();
            if ($this->repository_manager->current_user_has_recycled_objects())
            {
                $trash['class'] = 'trash_full';
            }
            else
            {
                $trash['class'] = 'trash';
            }

            $extra_items[] = $trash;
        }

        return $extra_items;
    }

    /**
     * Gets the URL of a given category
     *
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $category));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     *
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $str = Translation :: get('MyRepository');
            if (substr($crumb['title'], 0, strlen($str)) == $str)
                continue;
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
        }
        return $trail;
    }

    /**
     * Renders the menu as a tree
     *
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
    }
}
