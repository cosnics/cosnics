<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Domain;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Toolbar
{
    public const TYPE_HORIZONTAL = 'horizontal';
    public const TYPE_VERTICAL = 'vertical';

    /**
     * @var \Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem[]
     */
    private array $items = [];

    private string $type;

    public function __construct(string $type = self::TYPE_HORIZONTAL)
    {
        $this->type = $type;
    }

    public function render(): ?string
    {
        if (!$this->hasItems())
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

    public function addItem(ToolbarItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem[] $items
     */
    public function addItems(array $items): static
    {
        foreach ($items as $item)
        {
            $this->items[] = $item;
        }

        return $this;
    }

    public function getItem(int $index): ToolbarItem
    {
        return $this->items[$index];
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem[] $items
     */
    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }

    public function insertItem(ToolbarItem $item, int $index): static
    {
        $items = $this->items;
        array_splice($items, $index, 0, [$item]);
        $this->items = $items;

        return $this;
    }

    public function replaceItem(ToolbarItem $item, int $index): static
    {
        $items = $this->items;
        array_splice($items, $index, 1, [$item]);
        $this->items = $items;

        return $this;
    }
}
