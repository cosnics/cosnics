<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class Toolbar
{
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_VERTICAL = 'vertical';

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private $items = [];

    /**
     *
     * @var string[]
     */
    private $class_names = [];

    /**
     *
     * @var string
     */
    private $css = null;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @param string $type
     * @param string[] $class_names
     * @param string $css
     */
    public function __construct($type = self::TYPE_HORIZONTAL, $class_names = [], $css = null)
    {
        $this->type = $type;
        $this->class_names = $class_names;
        $this->css = $css;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        if (!$this->has_items())
        {
            return null;
        }

        $toolbar_data = $this->items;
        $type = $this->get_type();
        $class_names = $this->class_names;
        $css = $this->css;

        if (!is_array($class_names))
        {
            $class_names = array($class_names);
        }
        $class_names[] = 'toolbar_' . $type;

        $html = [];

        $html[] = '<div class="btn-toolbar btn-toolbar-cosnics">';
        $html[] = '<div class="btn-group ">';

        foreach ($toolbar_data as $index => $toolbar_item)
        {
            $classes = [];

            if ($index == 0)
            {
                $classes[] = 'first';
            }

            if ($index == count($toolbar_data) - 1)
            {
                $classes[] = 'last';
            }

            $html[] = $toolbar_item->render();
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode($html);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function add_item(ToolbarItem $item)
    {
        $this->items[] = $item;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function add_items($items)
    {
        foreach ($items as $item)
        {
            $this->items[] = $item;
        }
    }

    /**
     *
     * @return string
     * @deprecated Use render() now
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     *
     * @param boolean $keepDisplayProperty
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function convertToButtonToolBar($keepDisplayProperty = true)
    {
        $buttonToolbar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($this->get_items() as $item)
        {
            $buttonGroup->addButton(
                new Button(
                    $item->get_label(), $item->get_image(), $item->get_href(),
                    $keepDisplayProperty ? $item->get_display() : Button::DISPLAY_ICON_AND_LABEL,
                    $item->get_confirmation(), 'btn-link', $item->get_target()
                )
            );
        }

        $buttonToolbar->addItem($buttonGroup);

        return $buttonToolbar;
    }

    /**
     * Returns the toolbaritem from the given position
     *
     * @param integer $index
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem
     */
    public function get_item($index)
    {
        return $this->items[$index];
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_items()
    {
        return $this->items;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function set_items(array $items)
    {
        $this->items = $items;
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return boolean
     */
    public function has_items()
    {
        return count($this->items) > 0;
    }

    /**
     * Inserts an item in the toolbar
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     * @param integer $index
     */
    public function insert_item(ToolbarItem $item, $index)
    {
        $items = $this->items;
        array_splice($items, $index, 0, array($item));
        $this->items = $items;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function prepend_item(ToolbarItem $item)
    {
        array_unshift($this->items, $item);
    }

    /**
     * Replaces an item in the toolbar
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     * @param integer $index
     */
    public function replace_item(ToolbarItem $item, $index)
    {
        $items = $this->items;
        array_splice($items, $index, 1, array($item));
        $this->items = $items;
    }
}
