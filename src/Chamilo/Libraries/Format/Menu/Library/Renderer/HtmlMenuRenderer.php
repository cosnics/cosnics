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
     * Finish the tree level (for types 'tree' and 'sitemap')
     *
     * @param integer $level
     */
    public function finishLevel($level)
    {
    }

    /**
     * Finish the menu
     *
     * @param integer $level
     */
    public function finishMenu($level)
    {
    }

    /**
     * Finish the row in the menu
     *
     * @param integer $level
     */
    public function finishRow($level)
    {
    }

    /**
     * Renders the element of the menu
     *
     * @param string[] $node
     * @param integer $level
     * @param integer $type
     */
    public function renderEntry($node, $level, $type)
    {
    }

    /**
     *
     * @param string $menuType
     */
    public function setMenuType($menuType)
    {
        $this->_menuType = $menuType;
    }
}