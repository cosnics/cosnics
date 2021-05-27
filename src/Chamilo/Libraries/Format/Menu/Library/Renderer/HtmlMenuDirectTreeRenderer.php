<?php
namespace Chamilo\Libraries\Format\Menu\Library\Renderer;

use Exception;

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
class HtmlMenuDirectTreeRenderer extends HtmlMenuRenderer
{
    const HTML_MENU_ENTRY_ACTIVE = 1;
    const HTML_MENU_ENTRY_ACTIVEPATH = 2;
    const HTML_MENU_ENTRY_INACTIVE = 0;

    /**
     *
     * @var string
     */
    public $_html = '';

    /**
     *
     * @var string
     */
    public $_levelHtml = [];

    /**
     *
     * @var string
     */
    public $_itemHtml = [];

    /**
     *
     * @var string[]
     */
    public $_levelTemplate = array('<ul>', '</ul>');

    /**
     *
     * @var string[]
     */
    public $_itemTemplate = array('<li>', '</li>');

    /**
     *
     * @var string[]
     */
    public $_entryTemplates = array(
        self::HTML_MENU_ENTRY_INACTIVE => '<a href="{url}">{title}</a>',
        self::HTML_MENU_ENTRY_ACTIVE => '<strong>{title}</strong>',
        self::HTML_MENU_ENTRY_ACTIVEPATH => '<a href="{url}"><em>{title}</em></a>'
    );

    /**
     * Finish the tree level (for types 'tree' and 'sitemap')
     *
     * @param integer $level
     */
    public function finishLevel($level)
    {
        isset($this->_levelHtml[$level]) or $this->_levelHtml[$level] = '';
        $this->_levelHtml[$level] .= $this->_itemTemplate[0] . $this->_itemHtml[$level] . $this->_itemTemplate[1];

        if (0 < $level)
        {
            $this->_itemHtml[$level - 1] .= $this->_levelTemplate[0] . $this->_levelHtml[$level] .
                $this->_levelTemplate[1];
        }
        else
        {
            $this->_html = $this->_levelTemplate[0] . $this->_levelHtml[$level] . $this->_levelTemplate[1];
        }

        unset($this->_itemHtml[$level], $this->_levelHtml[$level]);
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
        if (!empty($this->_itemHtml[$level]))
        {
            isset($this->_levelHtml[$level]) or $this->_levelHtml[$level] = '';
            $this->_levelHtml[$level] .= $this->_itemTemplate[0] . $this->_itemHtml[$level] . $this->_itemTemplate[1];
        }

        $keys = $values = [];

        foreach ($node as $k => $v)
        {
            if ('sub' != $k && is_scalar($v))
            {
                $keys[] = '{' . $k . '}';
                $values[] = $v;
            }
        }

        $this->_itemHtml[$level] = str_replace($keys, $values, $this->_entryTemplates[$type]);
    }

    /**
     *
     * @param mixed either type (one of HTML_MENU_ENTRY_* constants) or an array 'type' => 'template'
     * @param string template for this entry type if $type is not an array
     */
    public function setEntryTemplate($type, $template = null)
    {
        if (is_array($type))
        {
            // array_merge() will not work here: the keys are numeric
            foreach ($type as $typeId => $typeTemplate)
            {
                if (isset($this->_entryTemplates[$typeId]))
                {
                    $this->_entryTemplates[$typeId] = $typeTemplate;
                }
            }
        }
        else
        {
            $this->_entryTemplates[$type] = $template;
        }
    }

    /**
     *
     * @param string this will be prepended to the entry HTML
     * @param string this will be appended to the entry HTML
     */
    public function setItemTemplate($prepend, $append)
    {
        $this->_itemTemplate = array($prepend, $append);
    }

    /**
     *
     * @param string this will be prepended to the submenu HTML
     * @param string this will be appended to the submenu HTML
     */
    public function setLevelTemplate($prepend, $append)
    {
        $this->_levelTemplate = array($prepend, $append);
    }

    /**
     *
     * @param string $menuType
     *
     * @throws \Exception
     */
    public function setMenuType($menuType)
    {
        if ('tree' == $menuType || 'sitemap' == $menuType)
        {
            $this->_menuType = $menuType;
        }
        else
        {
            throw new Exception("HTML_Menu_DirectTreeRenderer: unable to render '$menuType' type menu");
        }
    }

    /**
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->_html;
    }
}