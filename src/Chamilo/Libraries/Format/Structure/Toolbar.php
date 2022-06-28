<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class Toolbar
{
    public const TYPE_HORIZONTAL = 'horizontal';
    public const TYPE_VERTICAL = 'vertical';

    /**
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private array $items = [];

    private string $type;

    public function __construct(string $type = self::TYPE_HORIZONTAL)
    {
        $this->type = $type;
    }

    public function render(): ?string
    {
        if (!$this->has_items())
        {
            return null;
        }

        $html = [];

        $html[] = '<div class="btn-toolbar btn-toolbar-cosnics">';
        $html[] = '<div class="btn-group ">';

        foreach ($this->items as $toolbarItem)
        {
            $html[] = $toolbarItem->render();
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode($html);
    }

    public function add_item(ToolbarItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function add_items(array $items)
    {
        foreach ($items as $item)
        {
            $this->items[] = $item;
        }
    }

    /**
     * @deprecated Use Toolbar::render() now
     */
    public function as_html(): string
    {
        return $this->render();
    }

    public function convertToButtonToolBar(bool $keepDisplayProperty = true): ButtonToolBar
    {
        $buttonToolbar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($this->get_items() as $item)
        {
            $buttonGroup->addButton(
                new Button(
                    $item->get_label(), $item->get_image(), $item->get_href(),
                    $keepDisplayProperty ? $item->get_display() : AbstractButton::DISPLAY_ICON_AND_LABEL,
                    $item->get_confirmation(), ['btn-link'], $item->get_target()
                )
            );
        }

        $buttonToolbar->addItem($buttonGroup);

        return $buttonToolbar;
    }

    public function get_item(int $index): ToolbarItem
    {
        return $this->items[$index];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_items(): array
    {
        return $this->items;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function set_items(array $items)
    {
        $this->items = $items;
    }

    /**
     * @deprecated Use Toolbar::getType() now
     */
    public function get_type(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @deprecated Use Toolbar::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    public function has_items(): bool
    {
        return count($this->items) > 0;
    }

    public function insert_item(ToolbarItem $item, int $index)
    {
        $items = $this->items;
        array_splice($items, $index, 0, [$item]);
        $this->items = $items;
    }

    public function replace_item(ToolbarItem $item, int $index)
    {
        $items = $this->items;
        array_splice($items, $index, 1, [$item]);
        $this->items = $items;
    }
}
