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
        $create = array();
        $create['title'] = Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES);
        $create['url'] = $this->repository_manager->get_content_object_creation_url();
        $create['class'] = 'create';
        
        $templates = array();
        $templates['title'] = Translation :: get('BrowseTemplates');
        $templates['url'] = $this->repository_manager->get_url(
            array(Manager :: PARAM_ACTION => Manager :: ACTION_TEMPLATE));
        $templates['class'] = 'template';
        
        $import = array();
        $import['title'] = Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES);
        $import['url'] = $this->repository_manager->get_content_object_importing_url();
        $import['class'] = 'import';
        
        $quota = array();
        $quota['title'] = Translation :: get('Quota');
        $quota['url'] = $this->repository_manager->get_quota_url();
        $quota['class'] = 'quota';
        
        $pub = array();
        $pub['title'] = Translation :: get('MyPublications');
        $pub['url'] = $this->repository_manager->get_publication_url();
        $pub['class'] = 'publication';
        
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
        
        $doubles = array();
        $doubles['title'] = Translation :: get('ViewDoubles');
        $doubles['url'] = $this->repository_manager->get_view_doubles_url();
        $doubles['class'] = 'doubles';
        
        $extra_items[] = $pub;
        $extra_items[] = $create;
        $extra_items[] = $import;
        $extra_items[] = $templates;
        $extra_items[] = $quota;
        $extra_items[] = $doubles;
        $extra_items[] = $trash;
        
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
