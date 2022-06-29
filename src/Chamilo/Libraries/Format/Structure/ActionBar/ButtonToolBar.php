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
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function __construct(?string $searchUrl = null, array $items = [], array $classes = [])
    {
        $this->searchUrl = $searchUrl;
        $this->items = $items;
        $this->classes = $classes;
    }

    public function addButtonGroup(AbstractButtonToolBarItem $buttonToolBarItem)
    {
        $this->addItem($buttonToolBarItem);
    }

    public function addClass(string $class)
    {
        $this->classes[] = $class;
    }

    public function addItem(AbstractButtonToolBarItem $buttonToolBarItem)
    {
        $this->items[] = $buttonToolBarItem;
    }

    /**
     * @param AbstractButtonToolBarItem[] $buttonToolbarItems
     */
    public function addItems(array $buttonToolbarItems = [])
    {
        foreach ($buttonToolbarItems as $buttonToolbarItem)
        {
            $this->addItem($buttonToolbarItem);
        }
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
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
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
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function getSearchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function setSearchUrl(?string $searchUrl)
    {
        $this->searchUrl = $searchUrl;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }

    public function prependItem(AbstractButtonToolBarItem $buttonToolBarItem)
    {
        array_unshift($this->items, $buttonToolBarItem);
    }
}