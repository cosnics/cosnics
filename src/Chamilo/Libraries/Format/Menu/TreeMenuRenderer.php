<?php
namespace Chamilo\Libraries\Format\Menu;

use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuDirectTreeRenderer;

/**
 * Renderer which can be used to include a tree menu on your page.
 *
 * @package Chamilo\Libraries\Format\Menu
 * @author Bart Mollet
 * @author Tim De Pauw
 */
class TreeMenuRenderer extends HtmlMenuDirectTreeRenderer
{

    /**
     *
     * @var boolean
     */
    private static $initialized;

    /**
     *
     * @var string
     */
    private $search_url;

    /**
     *
     * @var string
     */
    private $tree_name;

    /**
     *
     * @var string
     */
    private $item_url;

    /**
     *
     * @var boolean
     */
    private $collapsed;

    /**
     *
     * @param string $treeName
     * @param string $searchUrl
     * @param string $itemUrl
     * @param boolean $collapsed
     */
    public function __construct($treeName = '', $searchUrl = '', $itemUrl = '#', $collapsed = true)
    {
        $this->search_url = $searchUrl;
        $this->tree_name = $treeName;
        $this->item_url = $itemUrl;
        $this->collapsed = $collapsed;

        $entryTemplates = array();
        $entryTemplates[self::HTML_MENU_ENTRY_INACTIVE] = '<div class="{children}"><a href="{url}" onclick="{onclick}" id="{id}" style="{style}" title="{safe_title}"><span class="{class}"></span> {title}</a></div>';
        $entryTemplates[self::HTML_MENU_ENTRY_ACTIVE] = '<!--A--><div><a href="{url}" onclick="{onclick}" id="{id}" style="{style}" title="{safe_title}"><span class="{class}"></span> {title}</a></div>';
        $entryTemplates[self::HTML_MENU_ENTRY_ACTIVEPATH] = '<!--P--><div><a href="{url}" onclick="{onclick}" id="{id}" style="{style}" title="{safe_title}"><span class="{class}"></span> {title}</a></div>';
        $this->setEntryTemplate($entryTemplates);
        $this->setItemTemplate('<li>', '</li>' . "\n");
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuDirectTreeRenderer::finishLevel()
     */
    public function finishLevel($level)
    {
        $root = ($level == 0);

        if ($root)
        {
            $this->setLevelTemplate(
                '<div id="' . $this->tree_name . '"><ul class="tree-menu">' . "\n",
                '</ul></div>' . "\n");
        }

        parent::finishLevel($level);

        if ($root)
        {
            $this->setLevelTemplate('<ul>' . "\n", '</ul>' . "\n");
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuDirectTreeRenderer::renderEntry()
     */
    public function renderEntry($node, $level, $type)
    {
        // Add some extra keys, so they always get replaced in the template.
        foreach (array('children', 'class', 'onclick', 'id') as $key)
        {
            if (! array_key_exists($key, $node))
            {
                $node[$key] = '';
            }
        }

        if (! $node['safe_title'])
        {
            $node['safe_title'] = htmlentities(strip_tags($node['title']));
        }

        parent::renderEntry($node, $level, $type);
    }

    /**
     * Gets a HTML representation of the tree menu
     *
     * @return string
     */
    public function toHtml()
    {
        $parent_html = parent::toHtml();

        $parent_html = str_replace('<li><!--A-->', '<li class="current">', $parent_html);
        $parent_html = str_replace('<li><!--P-->', '<li class="current_path">', $parent_html);
        $parent_html = preg_replace('/\s*\b(onclick|id)="\s*"\s*/', ' ', $parent_html);

        $html[] = $parent_html;

        $html[] = '<script type="text/javascript">';
        $html[] = '$("#' . $this->tree_name . '").tree_menu({search: "' . $this->search_url . '", item_url: "' .
             $this->item_url . '", collapsed: "' . $this->collapsed . '" });';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
