<?php
namespace Chamilo\Libraries\Format\Menu;

use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;

/**
 * Renderer which can be used to create an array of options to use in a select list.
 * The options are displayed in a hierarchical way in the select list.
 *
 * @package Chamilo\Libraries\Format\Menu
 */
class OptionsMenuRenderer extends HtmlMenuArrayRenderer
{
    const KEY_ID = 'id';

    /**
     * @var string[]
     */
    protected $exclude;

    /**
     * Create a new OptionsMenuRenderer
     *
     * @param string[] $exclude Which items should be excluded (based on the $key value in the menu items). The whole
     *        submenu of which the elements of the exclude array are the root elements will be excluded.
     */
    public function __construct($exclude = [])
    {
        $exclude = is_array($exclude) ? $exclude : array($exclude);
        $this->exclude = $exclude;
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
        // If this node is in the exclude list, add all its child-nodes to the exclude list
        if (in_array($node[self::KEY_ID], $this->exclude))
        {
            foreach ($node['sub'] as $child_id => $child)
            {
                if (!in_array($child_id, $this->exclude))
                {
                    $this->exclude[] = $child_id;
                }
            }
        }
        // else
        {
            unset($node['sub']);
            $node['level'] = $level;
            $node['type'] = $type;
            $this->_menuAry[] = $node;
        }
    }

    /**
     * Returns the resultant array
     *
     * @return string[]
     */
    public function toArray()
    {
        $array = parent::toArray();
        $choices = [];

        foreach ($array as $index => $item)
        {
            $prefix = '';

            if ($item['level'] > 0)
            {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $item['level'] - 1) . '&mdash; ';
            }

            $choices[$item[self::KEY_ID]] = $prefix . $item['title'];
        }

        return $choices;
    }
}
