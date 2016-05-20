<?php
namespace Chamilo\Libraries\Format\Menu\Library\Renderer;

/**
 * Originaly a PEAR library
 *
 * @package Chamilo\Libraries\Format\Menu\Library\Renderer
 * @author Alex Vorobiev <sasha@mathforum.com>
 * @author Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author Alexey Borzov <avb@php.net>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HtmlMenuRenderer
{

    /**
     *
     * @var string
     */
    public $_menuType;

    /**
     *
     * @param string menu type
     */
    public function setMenuType($menuType)
    {
        $this->_menuType = $menuType;
    }

    /**
     * Finish the menu
     *
     * @param int current depth in the tree structure
     */
    public function finishMenu($level)
    {
    }

    /**
     * Finish the tree level (for types 'tree' and 'sitemap')
     *
     * @param int current depth in the tree structure
     */
    public function finishLevel($level)
    {
    }

    /**
     * Finish the row in the menu
     *
     * @param int current depth in the tree structure
     */
    public function finishRow($level)
    {
    }

    /**
     * Renders the element of the menu
     *
     * @param array Element being rendered
     * @param int Current depth in the tree structure
     * @param int Type of the element (one of HTML_MENU_ENTRY_* constants)
     */
    public function renderEntry($node, $level, $type)
    {
    }
}