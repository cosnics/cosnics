<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBar
{

    /**
     *
     * @var string
     */
    private $searchUrl;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    private $items;

    /**
     *
     * @var string[]
     */
    private $classes;

    /**
     *
     * @param string $searchUrl
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     * @param string[] $classes
     */
    public function __construct($searchUrl = null, $items = array(), $classes = array())
    {
        $this->searchUrl = $searchUrl;
        $this->items = $items;
        $this->classes = $classes;
    }

    /**
     *
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->searchUrl;
    }

    /**
     *
     * @param string $searchUrl
     */
    public function setSearchUrl($searchUrl)
    {
        $this->searchUrl = $searchUrl;
    }

    /**
     *
     * @deprecated Use getButtonToolBarItems() now
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getButtonGroups()
    {
        return $this->getItems();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     *
     * @deprecated Use setButtonToolBarItems() now
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function setButtonGroups($items)
    {
        $this->setItems($items);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem $buttonToolBarItem
     */
    public function addButtonGroup($buttonToolBarItem)
    {
        $this->addItem($buttonToolBarItem);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem $buttonToolBarItem
     */
    public function addItem($buttonToolBarItem)
    {
        $this->items[] = $buttonToolBarItem;
    }

    /**
     *
     * @param AbstractButtonToolBarItem[] $buttonToolbarItems
     */
    public function addItems($buttonToolbarItems = array())
    {
        foreach ($buttonToolbarItems as $buttonToolbarItem)
        {
            $this->addItem($buttonToolbarItem);
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem $buttonToolBarItem
     */
    public function prependItem($buttonToolBarItem)
    {
        array_unshift($this->items, $buttonToolBarItem);
    }

    /**
     *
     * @return string[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     *
     * @param string[] $classes
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    /**
     *
     * @param string $class
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
    }

    /**
     * Returns whether or not the current button toolbar has items
     * 
     * @return bool
     */
    public function hasItems()
    {
        return count($this->items) > 0;
    }
}