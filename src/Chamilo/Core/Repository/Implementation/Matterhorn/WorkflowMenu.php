<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: category_menu.class.php 191 2009-11-13 11:50:28Z chellee $
 *
 * @package application.common.category_manager
 */
/**
 * This class provides a navigation menu to allow a user to browse through his reservations categories
 *
 * @author Sven Vanpoucke
 */
class WorkflowMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;
    const FEED_TYPE_SUCCEEDED = 'succeeded';
    const FEED_TYPE_FAILED = 'failed';
    const FEED_TYPE_ALL = 'all';
    const FEED_TYPE_RUNNING = 'running';
    const FEED_TYPE_STOPPED = 'stopped';

    private $current_item;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $external_repository_manager;

    private $menu_items;

    public function __construct($current_item, $external_repository_manager)
    {
        $this->current_item = $current_item;
        $this->external_repository_manager = $external_repository_manager;

        $menu_items = array();

        $succeeded = array();
        $succeeded['title'] = Translation :: get('Succeeded');
        $succeeded['url'] = $this->get_url(
            array(Manager :: PARAM_FOLDER => self :: FEED_TYPE_SUCCEEDED),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $succeeded['class'] = 'succeeded';
        $menu_items[] = $succeeded;

        $failed = array();
        $failed['title'] = Translation :: get('Failed');
        $failed['url'] = $this->get_url(
            array(Manager :: PARAM_FOLDER => self :: FEED_TYPE_FAILED),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $failed['class'] = 'failed';
        $menu_items[] = $failed;

        $running = array();
        $running['title'] = Translation :: get('Running');
        $running['url'] = $this->get_url(
            array(Manager :: PARAM_FOLDER => self :: FEED_TYPE_RUNNING),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $running['class'] = 'running';
        $menu_items[] = $running;

        $running = array();
        $running['title'] = Translation :: get('Stopped');
        $running['url'] = $this->get_url(
            array(Manager :: PARAM_FOLDER => self :: FEED_TYPE_STOPPED),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $running['class'] = 'stopped';
        $menu_items[] = $running;

        $all = array();
        $all['title'] = Translation :: get('All');
        $all['url'] = $this->get_url(
            array(Manager :: PARAM_FOLDER => self :: FEED_TYPE_ALL),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $all['class'] = 'all';
        $menu_items[] = $all;

        $this->menu_items = $menu_items;

        parent :: __construct($menu_items);

        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url());
    }

    public function get_menu_items()
    {
        return $this->menu_items;
    }

    public function count_menu_items()
    {
        return count($this->menu_items);
    }

    private function get_url($parameters, $filters)
    {
        return $this->external_repository_manager->get_url($parameters, $filters);
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
            if ($crumb['title'] == Translation :: get('ExternalRepositorys'))
                continue;
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
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
