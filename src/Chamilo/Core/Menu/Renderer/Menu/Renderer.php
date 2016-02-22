<?php
namespace Chamilo\Core\Menu\Renderer\Menu;

use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
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
    const TYPE_SITE_MAP = 'SiteMap';
    const TYPE_BAR = 'Bar';

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
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

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
        $class = __NAMESPACE__ . '\Type\\' . $type;
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
        return self :: factory($type, $request, $user)->render();
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService()
    {
        if (! isset($this->itemService))
        {
            $this->itemService = new ItemService(new ItemRepository());
        }
        
        return $this->itemService;
    }

    public function getRootItems()
    {
        return $this->getItemService()->getItemsByParentIdentifier(0);
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
        
        $userRights = $this->getItemService()->determineRightsForUser($this->get_user());
        
        $html = array();
        
        $html[] = $this->display_menu_header();
        
        $category_items = array();
        
        foreach ($this->getRootItems() as $item)
        {
            if ($userRights[$item->get_id()])
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

    abstract public function display_menu_header();

    abstract public function display_menu_footer();
}
