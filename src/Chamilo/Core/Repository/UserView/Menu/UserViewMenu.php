<?php
namespace Chamilo\Core\Repository\UserView\Menu;

use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserViewMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * The array renderer used to determine the breadcrumbs.
     *
     * @var HTML_Menu_ArrayRenderer
     */
    private $array_renderer;

    /**
     * The string passed to sprintf() to format category URLs
     *
     * @var string
     */
    private $url_format;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param int $current_user_view_id
     * @param string $url_format
     */
    public function __construct(
        Application $application, $current_user_view_id = null, $url_format = '?view=%s'
    )
    {
        $this->application = $application;
        $this->url_format = $url_format;

        parent::__construct($this->get_menu_items());

        $this->array_renderer = new HtmlMenuArrayRenderer();

        if ($current_user_view_id)
        {
            $this->forceCurrentUrl($this->get_view_url($current_user_view_id));
        }
    }

    /**
     *
     * @return \libraries\format\BreadcrumbTrail
     */
    public function get_breadcrumbs()
    {
        $trail = $this->getBreadcrumbTrail();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $str = Translation::get('MyRepository');
            if (substr($crumb['title'], 0, strlen($str)) == $str)
            {
                continue;
            }
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
        }

        return $trail;
    }

    /**
     * Returns the menu items.
     *
     * @return mixed[]
     */
    private function get_menu_items()
    {
        $menu = [];
        $menu_item = [];

        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView::class, UserView::PROPERTY_USER_ID),
            new StaticConditionVariable($this->application->get_user_id())
        );
        $userviews = DataManager::retrieves(UserView::class, new DataClassRetrievesParameters($condition));

        $userview = [];
        $userview['title'] = Translation::get('UserViews');
        $userview['url'] = $this->application->get_url(
            array(
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_USER_VIEW,
                GenericTabsRenderer::PARAM_SELECTED_TAB => array(
                    \Chamilo\Core\Repository\Manager::TABS_FILTER => \Chamilo\Core\Repository\Manager::TAB_USERVIEW
                ),
                Manager::PARAM_ACTION => Manager::ACTION_BROWSE
            ), array(\Chamilo\Core\Repository\Manager::PARAM_CATEGORY_ID)
        );

        $glyph = new FontAwesomeGlyph('search', [], null, 'fas');
        $userview['class'] = $glyph->getClassNamesString();
        $menu[] = $userview;

        foreach ($userviews as $userview)
        {
            $menu_item = [];
            $menu_item['title'] = $userview->get_name();
            $menu_item['url'] = $this->get_view_url($userview->get_id());
            $menu_item['class'] = '';
            $menu_item[OptionsMenuRenderer::KEY_ID] = $userview->get_id();
            $menu[] = $menu_item;
        }

        return $menu;
    }

    /**
     *
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    /**
     *
     * @param int $user_view_id
     *
     * @return string
     */
    private function get_view_url($user_view_id)
    {
        return htmlentities(str_replace('__VIEW__', $user_view_id, $this->url_format));
    }

    /**
     *
     * @return string
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
