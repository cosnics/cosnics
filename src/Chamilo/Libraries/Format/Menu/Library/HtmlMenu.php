<?php
namespace Chamilo\Libraries\Format\Menu\Library;

/**
 * Originaly a PEAR library
 *
 * @package Chamilo\Libraries\Format\Menu\Library
 * @author Alex Vorobiev <sasha@mathforum.com>
 * @author Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author Alexey Borzov <avb@php.net>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlMenu
{
    const HTML_MENU_ENTRY_ACTIVE = 1;

    const HTML_MENU_ENTRY_ACTIVEPATH = 2;

    const HTML_MENU_ENTRY_BREADCRUMB = 6;

    const HTML_MENU_ENTRY_INACTIVE = 0;

    const HTML_MENU_ENTRY_NEXT = 4;

    const HTML_MENU_ENTRY_PREVIOUS = 3;

    const HTML_MENU_ENTRY_UPPER = 5;

    /**
     * Menu structure as a multidimensional hash.
     *
     * @var string[]
     */
    var $_menu = [];

    /**
     * Mapping from URL to menu path.
     *
     * @var array
     * @see getPath()
     */
    var $_urlMap = [];

    /**
     * Path to the current menu item.
     *
     * @var string[]
     */
    var $_path = [];

    /**
     * Menu type: tree, rows, you-are-here.
     *
     * @var string
     */
    var $_menuType = 'tree';

    /**
     *
     * @var string
     */
    var $_forcedUrl = '';

    /**
     * URL of the current page.
     *
     * @var string
     */
    var $_currentUrl = '';

    /**
     * The renderer being used to output the menu
     *
     * @var \Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuRenderer
     */
    var $_renderer = null;

    /**
     * Prefix for menu URLs
     *
     * @var string
     */
    var $_urlPrefix = '';

    /**
     * Initializes the menu, sets the type and menu structure.
     *
     * @param string[] $menu
     */
    public function __construct($menu = null)
    {
        if (is_array($menu))
        {
            $this->setMenu($menu);
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuRenderer $renderer
     * @param string string $menuType
     *
     * @return mixed
     */
    public function render($renderer, $menuType = '')
    {
        if ('' != $menuType)
        {
            $this->setMenuType($menuType);
        }

        $this->_renderer = $renderer;
        // the renderer will throw an error if it is unable to process this menu type
        $res = $this->_renderer->setMenuType($this->_menuType);

        if (is_object($res) && is_a($res, 'PEAR_Error'))
        {
            return $res;
        }

        // storing to a class variable saves some recursion overhead
        $this->_path = $this->getPath();

        switch ($this->_menuType)
        {
            case 'rows' :
                $this->_renderRows($this->_menu);
                break;

            case 'prevnext' :
                $this->_renderPrevNext($this->_menu);
                break;

            case 'urhere' :
                $this->_renderURHere($this->_menu);
                break;

            default :
                $this->_renderTree($this->_menu);
        }
    }

    /**
     * Builds the mappings from node url to the 'path' in the menu
     *
     * @param string[] $menu (sub)menu being processed
     * @param string[] $path path to the (sub)menu
     *
     * @return boolean true if the path to the current page was found, otherwise false.
     * @see getPath(), $_urlMap
     */
    private function _buildUrlMap($menu, $path)
    {
        foreach ($menu as $nodeId => $node)
        {
            $url = $node['url'];
            $this->_urlMap[$url] = $path;

            if ($url == $this->_currentUrl)
            {
                return true;
            }

            if (isset($node['sub']) && $this->_buildUrlMap($node['sub'], array_merge($path, array($nodeId))))
            {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param mixed $nodeId
     * @param string $nodeUrl Node 'url' attribute
     * @param integer $level Level in the tree
     *
     * @return integer Node type (one of HTML_MENU_ENTRY_* constants)
     */
    private function _findNodeType($nodeId, $nodeUrl, $level)
    {
        if ($this->_currentUrl == $nodeUrl)
        {
            // menu item that fits to this url - 'active' menu item
            return self::HTML_MENU_ENTRY_ACTIVE;
        }
        elseif (isset($this->_path[$level]) && $this->_path[$level] == $nodeId)
        {
            // processed menu item is part of the path to the active menu item
            return 'urhere' == $this->_menuType ? self::HTML_MENU_ENTRY_BREADCRUMB : self::HTML_MENU_ENTRY_ACTIVEPATH;
        }
        else
        {
            // not selected, not a part of the path to the active menu item
            return self::HTML_MENU_ENTRY_INACTIVE;
        }
    }

    /**
     * Renders the 'prevnext' menu
     *
     * @param string[] $menu (sub)menu being rendered
     * @param integer $level current depth in the tree structure
     * @param integer $flagStop flag indicating whether to finish processing
     *        (0 - continue, 1 - this is "next" node, 2 - stop)
     */
    private function _renderPrevNext($menu, $level = 0, $flagStop = 0)
    {
        static $last_node = [], $up_node = [];

        foreach ($menu as $node_id => $node)
        {
            if (0 != $flagStop)
            {
                // add this item to the menu and stop recursion - (next >>) node
                if ($flagStop == 1)
                {
                    $this->_renderer->renderEntry($node, $level, self::HTML_MENU_ENTRY_NEXT);
                    $flagStop = 2;
                }

                break;
            }
            else
            {
                $type = $this->_findNodeType($node_id, $node['url'], $level);
                if (self::HTML_MENU_ENTRY_ACTIVE == $type)
                {
                    $flagStop = 1;

                    // WARNING: if there's no previous take the first menu entry - you might not like this rule!
                    if (0 == count($last_node))
                    {
                        reset($this->_menu);
                        list($node_id, $last_node) = each($this->_menu);
                    }

                    $this->_renderer->renderEntry($last_node, $level, self::HTML_MENU_ENTRY_PREVIOUS);

                    // WARNING: if there's no up take the first menu entry - you might not like this rule!
                    if (0 == count($up_node))
                    {
                        reset($this->_menu);
                        list($node_id, $up_node) = each($this->_menu);
                    }

                    $this->_renderer->renderEntry($up_node, $level, self::HTML_MENU_ENTRY_UPPER);
                }
            }

            // remember the last (<< prev) node
            $last_node = $node;

            if (isset($node['sub']))
            {
                if (self::HTML_MENU_ENTRY_INACTIVE != $type)
                {
                    $up_node = $node;
                }

                $flagStop = $this->_renderPrevNext($node['sub'], $level + 1, $flagStop);
            }
        }

        if (0 == $level)
        {
            $this->_renderer->finishRow($level);
            $this->_renderer->finishMenu($level);
        }

        return $flagStop;
    }

    /**
     * Renders the 'rows' menu
     *
     * @param string[] $menu (sub)menu being rendered
     * @param integer $level current depth in the tree structure
     */
    private function _renderRows($menu, $level = 0)
    {
        $submenu = false;

        foreach ($menu as $node_id => $node)
        {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            $this->_renderer->renderEntry($node, $level, $type);

            // follow the subtree if the active menu item is in it
            if (self::HTML_MENU_ENTRY_INACTIVE != $type && isset($node['sub']))
            {
                $submenu = $node['sub'];
            }
        }

        // every (sub)menu has its own table
        $this->_renderer->finishRow($level);
        $this->_renderer->finishMenu($level);

        // go deeper if neccessary
        if ($submenu)
        {
            $this->_renderRows($submenu, $level + 1);
        }
    }

    /**
     * Renders the tree menu ('tree' and 'sitemap')
     *
     * @param string[] $menu (sub)menu being rendered
     * @param integer $level current depth in the tree structure
     */
    private function _renderTree($menu, $level = 0)
    {
        foreach ($menu as $node_id => $node)
        {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            $this->_renderer->renderEntry($node, $level, $type);
            $this->_renderer->finishRow($level);

            // follow the subtree if the active menu item is in it or if we
            // want the full menu or if node expansion is forced (request #4391)
            if (isset($node['sub']) && ('sitemap' == $this->_menuType || self::HTML_MENU_ENTRY_INACTIVE != $type ||
                    !empty($node['forceExpand'])))
            {

                $this->_renderTree($node['sub'], $level + 1);
            }
        }

        $this->_renderer->finishLevel($level);

        if (0 == $level)
        {
            $this->_renderer->finishMenu($level);
        }
    }

    /**
     * Renders the 'urhere' menu
     *
     * @param string[] $menu (sub)menu being rendered
     * @param integer $level current depth in the tree structure
     */
    private function _renderURHere($menu, $level = 0)
    {
        foreach ($menu as $node_id => $node)
        {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            if (self::HTML_MENU_ENTRY_INACTIVE != $type)
            {
                $this->_renderer->renderEntry($node, $level, $type);
                // follow the subtree if the active menu item is in it
                if (isset($node['sub']))
                {
                    $this->_renderURHere($node['sub'], $level + 1);
                }
            }
        }

        if (0 == $level)
        {
            $this->_renderer->finishRow($level);
            $this->_renderer->finishMenu($level);
        }
    }

    /**
     *
     * @param string $url Url to use
     */
    public function forceCurrentUrl($url)
    {
        $this->_forcedUrl = $url;
    }

    /**
     *
     * @return string
     */
    public function getCurrentURL()
    {
        if (!empty($this->_forcedUrl))
        {
            return $this->_forcedUrl;
        }
        elseif (isset($_SERVER['PHP_SELF']))
        {
            return $_SERVER['PHP_SELF'];
        }
        elseif (isset($GLOBALS['PHP_SELF']))
        {
            return $GLOBALS['PHP_SELF'];
        }
        elseif ($env = getenv('PHP_SELF'))
        {
            return $env;
        }
        else
        {
            return '';
        }
    }

    /**
     * Returns the path of the current page in the menu 'tree'.
     *
     * @return string[] path to the selected menu item
     */
    public function getPath()
    {
        $this->_currentUrl = $this->getCurrentURL();
        $this->_buildUrlMap($this->_menu, []);

        // If there is no match for the current URL, try to come up with
        // the best approximation by shortening the url
        while ($this->_currentUrl && !isset($this->_urlMap[$this->_currentUrl]))
        {
            $this->_currentUrl = substr($this->_currentUrl, 0, - 1);
        }

        return isset($this->_urlMap[$this->_currentUrl]) ? $this->_urlMap[$this->_currentUrl] : [];
    }

    /**
     *
     * @param string[] $menu
     */
    public function setMenu($menu)
    {
        $this->_menu = $menu;
        $this->_urlMap = [];
    }

    /**
     *
     * @param string $menuType
     */
    public function setMenuType($menuType)
    {
        $menuType = strtolower($menuType);

        if (in_array($menuType, array('tree', 'rows', 'urhere', 'prevnext', 'sitemap')))
        {
            $this->_menuType = $menuType;
        }
        else
        {
            $this->_menuType = 'tree';
        }
    }
}