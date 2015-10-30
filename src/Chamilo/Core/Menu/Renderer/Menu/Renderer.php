<?php
namespace Chamilo\Core\Menu\Renderer\Menu;

use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataClass\LinkApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\File\Cache\FilesystemCache;
use Chamilo\Libraries\File\Path;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{
    const TYPE_BAR = 'bar';
    const TYPE_SITE_MAP = 'site_map';

    /**
     *
     * @var User null
     */
    private $user;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * The layout of the menubar
     *
     * @var String
     */
    protected $html;

    /**
     *
     * @param $user User|null
     */
    public function __construct(Request $request, $user = null)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     *
     * @return User null
     */
    public function get_user()
    {
        return $this->user;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param $type string
     * @param $user User|null
     * @return Renderer
     */
    public static function factory($type, Request $request, $user)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();
        return new $class($request, $user);
    }

    /**
     *
     * @param $type string
     * @param $user User|null
     * @return string
     */
    public static function toHtml($type, Request $request, $user)
    {
        // $cache = new FilesystemCache(Path :: getInstance()->getCachePath(__NAMESPACE__));
        // $cacheIdentifier = md5(serialize(array(__METHOD__, $type, $user->get_id())));

        // if (! $cache->contains($cacheIdentifier))
        // {
        $menu = self :: factory($type, $request, $user)->render();
        return $menu;
        // $cache->save($cacheIdentifier, $menu);
        // }

        // return $cache->fetch($cacheIdentifier);
    }

    public function get_menu_items()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_PARENT),
            new StaticConditionVariable(0));
        $order_by = array();
        $order_by[] = new OrderBy(new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_SORT));

        $parameters = new DataClassRetrievesParameters($condition, null, null, $order_by);
        return DataManager :: retrieves(Item :: class_name(), $parameters);
    }

    /**
     * Renders the menu
     *
     * @return string
     */
    public function render()
    {
        $user = $this->get_user();

        if (! $user)
        {
            return;
        }

        $items = $this->get_menu_items();

        $current_section = Page :: getInstance()->getSection();

        $html = array();

        $html[] = $this->display_menu_header($current_section);

        $category_items = array();

        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();

        while ($item = $items->next_result())
        {
            if (Rights :: get_instance()->is_allowed(
                Rights :: VIEW_RIGHT,
                Manager :: context(),
                null,
                $entities,
                $item->get_id(),
                Rights :: TYPE_ITEM))
            {
                if (! $item->is_hidden())
                {
                    $html[] = \Chamilo\Core\Menu\Renderer\Item\Renderer :: toHtml($this, $item);
                }
            }
        }

        $html[] = $this->display_menu_footer();

        return implode(PHP_EOL, $html);
    }

    abstract public function display_menu_header($current_section);

    abstract public function display_menu_footer();

    public function get_external_instance_menu_items()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_ENABLED),
            new StaticConditionVariable(1));

        $parameters = new DataClassRetrievesParameters(
            $condition,
            null,
            null,
            new OrderBy(
                new PropertyConditionVariable(
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_TITLE)));
        $external_instance_managers = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
            \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
            $parameters);

        $external_instance_manager_items = array();

        while ($external_instance_manager = $external_instance_managers->next_result())
        {
            if ($external_instance_manager instanceof \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance &&
                 $external_instance_manager->is_enabled())
            {
                $external_instance_manager_sub_item = new LinkApplicationItem();
                $item_title = new ItemTitle();
                $item_title->set_title($external_instance_manager->get_title());
                $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
                $item_titles = new ItemTitles(new ArrayResultSet(array($item_title)));

                $external_instance_manager_sub_item->set_titles($item_titles);

                $redirect = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => $external_instance_manager->get_type(),
                        \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY => $external_instance_manager->get_id()));

                $external_instance_manager_sub_item->set_url($redirect->getUrl());

                $external_instance_manager_sub_item->set_target(LinkItem :: TARGET_SELF);
                $external_instance_manager_sub_item->set_section($external_instance_manager->get_type());
                $external_instance_manager_sub_item->set_parent(1);
                $external_instance_manager_items[] = $external_instance_manager_sub_item;
            }
        }

        return $external_instance_manager_items;
    }
}
