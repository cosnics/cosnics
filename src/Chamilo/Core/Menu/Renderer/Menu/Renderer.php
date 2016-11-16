<?php
namespace Chamilo\Core\Menu\Renderer\Menu;

use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Symfony\Component\HttpFoundation\Request;
use Chamilo\Core\User\Storage\DataClass\User;

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
     * @var string
     */
    private $containerMode;

    /**
     *
     * @param $user User|null
     */
    public function __construct($containerMode, Request $request = null, $user = null)
    {
        $this->containerMode = $containerMode;
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
     * @return string
     */
    public function getContainerMode()
    {
        return $this->containerMode;
    }

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     *
     * @param $type string
     * @param $user User|null
     * @return Renderer
     */
    public static function factory($type, $containerMode, Request $request = null, $user = null)
    {
        $class = __NAMESPACE__ . '\Type\\' . $type;
        return new $class($containerMode, $request, $user);
    }

    /**
     *
     * @param $type string
     * @param $user User|null
     * @return string
     */
    public static function toHtml($type, $containerMode = 'container-fluid', Request $request = null, $user = null)
    {
        return self::factory($type, $containerMode, $request, $user)->render();
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
        
        if (! $user instanceof User && ! $this->isMenuAvailableAnonymously())
        {
            return;
        }
        
        $html = array();
        
        $numberOfItems = 0;
        $itemRenditions = array();
        
        if ($user)
        {
            $userRights = $this->getItemService()->determineRightsForUser($user);
            
            foreach ($this->getRootItems() as $item)
            {
                if ($userRights[$item->get_id()])
                {
                    if (! $item->is_hidden())
                    {
                        $itemRendition = \Chamilo\Core\Menu\Renderer\Item\Renderer::toHtml($this, $item);
                        if (! empty($itemRendition))
                        {
                            $numberOfItems ++;
                            $itemRenditions[] = $itemRendition;
                        }
                    }
                }
            }
        }
        
        $html[] = $this->display_menu_header($numberOfItems);
        $html[] = implode(PHP_EOL, $itemRenditions);
        $html[] = $this->display_menu_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns whether or not the menu is available for anonymous users
     */
    public function isMenuAvailableAnonymously()
    {
        return false;
    }

    abstract public function display_menu_header($numberOfItems = 0);

    abstract public function display_menu_footer();
}
