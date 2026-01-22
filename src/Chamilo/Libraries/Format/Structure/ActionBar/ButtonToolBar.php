<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBar
{

    /**
     * @var string[]
     */
    private array $classes;

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    private array $items;

    private ?string $searchUrl;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function __construct(?string $searchUrl = null, array $items = [], array $classes = [])
    {
        $this->searchUrl = $searchUrl;
        $this->items = $items;
        $this->classes = $classes;
    }

    public function addButtonGroup(AbstractButtonToolBarItem $buttonToolBarItem): static
    {
        $this->addItem($buttonToolBarItem);

        return $this;
    }

    public function addClass(string $class): static
    {
        $this->classes[] = $class;

        return $this;
    }

    public function addItem(AbstractButtonToolBarItem $buttonToolBarItem): static
    {
        $this->items[] = $buttonToolBarItem;

        return $this;
    }

    /**
     * @param AbstractButtonToolBarItem[] $buttonToolbarItems
     */
    public function addItems(array $buttonToolbarItems = []): static
    {
        foreach ($buttonToolbarItems as $buttonToolbarItem)
        {
            $this->addItem($buttonToolbarItem);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string[] $classes
     */
    public function setClasses(array $classes): static
    {
        $this->classes = $classes;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getSearchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function setSearchUrl(?string $searchUrl): static
    {
        $this->searchUrl = $searchUrl;

        return $this;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }

    public function prependItem(AbstractButtonToolBarItem $buttonToolBarItem): static
    {
        array_unshift($this->items, $buttonToolBarItem);

        return $this;
    }
}