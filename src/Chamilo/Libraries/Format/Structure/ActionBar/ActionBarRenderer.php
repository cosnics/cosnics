<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Chamilo\Libraries\Format\Structure\ToolbarItem;

/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions and a right
 * menu for a search bar.
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionBarRenderer
{
    // Item types
    const ITEM_TYPE_LEFT = 'left';
    const ITEM_TYPE_MIDDLE = 'middle';
    const ITEM_TYPE_RIGHT = 'right';

    // Rendering types
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_VERTICAL = 'vertical';

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private $actions = array(
        self :: ITEM_TYPE_LEFT => array(),
        self :: ITEM_TYPE_MIDDLE => array(),
        self :: ITEM_TYPE_RIGHT => array());

    private $searchForm;

    /**
     *
     * @var string
     */
    private $searchUrl;

    /**
     *
     * @var unknown
     */
    private $searchLocation;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @param string $type
     * @param string $name
     */
    public function __construct($type, $name = 'component')
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_type($type)
    {
        $this->type = $type;
    }

    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param string $itemType
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function addItem($itemType = self :: ITEM_TYPE_LEFT, ToolbarItem $item)
    {
        $this->actions[$itemType][] = $item;
    }

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     * @deprecated Use addItem($itemType, $item) now
     */
    public function add_action($type = self :: ITEM_TYPE_LEFT, $action)
    {
        $this->addItem($type, $action);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function addLeftItem(ToolbarItem $item)
    {
        $this->addItem(self :: ITEM_TYPE_LEFT, $item);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     * @deprecated Use addLeftItem($item) now
     */
    public function add_common_action($action)
    {
        $this->addLeftItem($action);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function addMiddleItem(ToolbarItem $item)
    {
        $this->addItem(self :: ITEM_TYPE_MIDDLE, $item);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     * @deprecated Use addMiddleItem($item) now
     */
    public function add_tool_action($action)
    {
        $this->addMiddleItem($action);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $item
     */
    public function addRightItem(ToolbarItem $item)
    {
        $this->addItem(self :: ITEM_TYPE_RIGHT, $item);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getLeftItems()
    {
        return $this->actions[self :: ITEM_TYPE_LEFT];
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getMiddleItems()
    {
        return $this->actions[self :: ITEM_TYPE_MIDDLE];
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getRightItems()
    {
        return $this->actions[self :: ITEM_TYPE_RIGHT];
    }

    /**
     *
     * @deprecated Use getMiddleItems() now
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_tool_actions()
    {
        return $this->getMiddleItems();
    }

    /**
     *
     * @deprecated Use getLeftItems() now
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_common_actions()
    {
        return $this->getLeftItems();
    }

    /**
     *
     * @deprecated Use getSearchUrl() now
     * @return string
     */
    public function get_search_url()
    {
        return $this->getSearchUrl();
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
     * @param string $itemType
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function setItems($itemType = self :: ITEM_TYPE_LEFT, $items)
    {
        $this->actions[$itemType] = $items;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function setLeftItems($items)
    {
        $this->setItems(self :: ITEM_TYPE_LEFT, $items);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function setMiddleItems($items)
    {
        $this->setItems(self :: ITEM_TYPE_MIDDLE, $items);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $items
     */
    public function setRightItems($items)
    {
        $this->setItems(self :: ITEM_TYPE_RIGHT, $items);
    }

    /**
     *
     * @deprecated Use setMiddleItems($items) now
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $actions
     */
    public function set_tool_actions($actions)
    {
        $this->setMiddleItems($actions);
    }

    /**
     *
     * @deprecated Use setLeftItems($items) now
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $actions
     */
    public function set_common_actions($actions)
    {
        $this->setLeftItems($actions);
    }

    /**
     *
     * @param string $searchUrl
     */
    public function setSearchUrl($searchUrl, $searchLocation = self :: ITEM_TYPE_RIGHT)
    {
        $this->searchUrl = $searchUrl;
        $this->searchLocation = $searchLocation;
        $this->searchForm = new ActionBarSearchForm($searchUrl);
    }

    /**
     *
     * @param string $searchUrl
     * @deprecated Use setSearchUrl($searchUrl) now
     */
    public function set_search_url($searchUrl)
    {
        $this->setSearchUrl($searchUrl);
    }

    /**
     *
     * @deprecated Use render() now
     * @return string
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     *
     * @param string $itemType
     * @param unknown $type
     * @return string
     */
    protected function renderToolbar($itemType)
    {
        $actionBarItemRenderer = new ActionBarItemRenderer();

        $items = $this->actions[$itemType];

        $html[] = '<div class="action-bar btn-group btn-group-sm">';

        if ($items && count($items) >= 0)
        {
            foreach ($items as $item)
            {
                $html[] = $actionBarItemRenderer->render($item);
            }
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    public function render()
    {
        $leftItems = $this->getLeftItems();
        $middleItems = $this->getMiddleItems();
        $rightItems = $this->getRightItems();

        if (count($leftItems) == 0 && count($middleItems) == 0 && count($rightItems) == 0 && is_null($this->searchForm))
        {
            return '';
        }

        $html = array();

        $html[] = '<div class="action-bar">';
        $html[] = '<div class="btn-toolbar">';

        $html[] = $this->renderToolbar(self :: ITEM_TYPE_LEFT);
        $html[] = $this->renderToolbar(self :: ITEM_TYPE_MIDDLE);
        $html[] = $this->renderToolbar(self :: ITEM_TYPE_RIGHT);

        if (! is_null($this->searchForm))
        {
            $searchForm = $this->searchForm;
            if ($searchForm)
            {
                if ($searchForm->validate())
                {
                    if ($this->clear_form_submitted())
                    {
                        $redirectResponse = new RedirectResponse($this->get_search_url());
                        $redirectResponse->send();
                    }
                }

                $html[] = $searchForm->as_html();
            }
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function clear_form_submitted()
    {
        return ! is_null(Request :: post('clear'));
    }

    public function get_query()
    {
        if ($this->searchForm)
        {
            return $this->searchForm->get_query();
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the search query conditions
     *
     * @param array $properties
     * @return Condition
     * @uses Utilities :: query_to_condition() (deprecated)
     */
    public function get_conditions($properties = array ())
    {
        // check input parameter
        if (! is_array($properties))
        {
            $properties = array($properties);
        }

        // get query
        $query = $this->get_query();

        // only process if we have a search query and properties
        if (isset($query) && count($properties))
        {
            $search_conditions = Utilities :: query_to_condition($query, $properties);

            $condition = $search_conditions;
        }
        return $condition;
    }
}
