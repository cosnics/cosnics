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
     * @param string $searchUrl
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $items
     */
    public function __construct($searchUrl = null, $items = array())
    {
        $this->searchUrl = $searchUrl;
        $this->items = $items;
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
}