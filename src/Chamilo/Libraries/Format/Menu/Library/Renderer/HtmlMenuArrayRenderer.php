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
class HtmlMenuArrayRenderer extends HtmlMenuRenderer
{

    /**
     *
     * @var string[][]
     */
    public $_ary = array();

    /**
     *
     * @var string[]
     */
    public $_menuAry = array();

    /**
     * Finish the menu
     *
     * @param integer $level
     */
    public function finishMenu($level)
    {
        if ('rows' == $this->_menuType)
        {
            $this->_ary[] = $this->_menuAry;
        }
        else
        {
            $this->_ary = $this->_menuAry;
        }
        $this->_menuAry = array();
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
        unset($node['sub']);
        $node['level'] = $level;
        $node['type'] = $type;
        $this->_menuAry[] = $node;
    }

    /**
     * Returns the resultant array
     *
     * @return string[][]
     */
    public function toArray()
    {
        return $this->_ary;
    }
}