<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    public function setSearchUrl($searchUrl)
    {
        $this->searchUrl = $searchUrl;
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

    public function render()
    {
        $type = $this->type;

        switch ($type)
        {
            case self :: TYPE_HORIZONTAL :
                return $this->renderHorizontal();
                break;
            case self :: TYPE_VERTICAL :
                return $this->renderVertical();
                break;
            default :
                return $this->renderHorizontal();
                break;
        }
    }

    public function renderHorizontal()
    {
        $leftItems = $this->getLeftItems();
        $middleItems = $this->getMiddleItems();

        if (count($leftItems) == 0 && count($middleItems) == 0 && is_null($this->searchForm))
        {
            return '';
        }

        $html = array();

        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '<div id="' . $this->get_name() . '_action_bar" class="action_bar">';
        $html[] = '<div class="bevel">';

        $html[] = '<table cellspacing="0">';
        $html[] = '<tr>';
        $html[] = '<td class="common_menu split">';

        if ($leftItems && count($leftItems) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($leftItems);
            $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);

            $html[] = $toolbar->as_html();
        }

        $html[] = '</td>';

        $html[] = '<td class="tool_menu split split_bevel">';

        if ($middleItems && count($middleItems) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($middleItems);
            $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);

            $html[] = $toolbar->as_html();
        }

        $html[] = '</td>';

        $html[] = '<td class="search_menu split_bevel">';

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

                $html[] = '<div class="searchForm">';
                $html[] = $searchForm->as_html();
                $html[] = '</div>';
            }
        }

        $html[] = '</td>';

        $html[] = '</tr>';
        $html[] = '</table>';

        $html[] = '<div class="clear"></div>';
        $html[] = '<div id="' . $this->get_name() . '_action_bar_hide_container" class="action_bar_hide_container">';
        $html[] = '<a id="' . $this->get_name() . '_action_bar_hide" class="action_bar_hide" href="#"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/AjaxHide') . '" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'ActionBarHorizontal.js');

        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }

    public function clear_form_submitted()
    {
        return ! is_null(Request :: post('clear'));
    }

    public function renderVertical()
    {
        $leftItems = $this->getLeftItems();
        $middleItems = $this->getMiddleItems();

        if (count($leftItems) == 0 && count($middleItems) == 0 && is_null($this->searchForm))
        {
            return '';
        }

        $html = array();

        $html[] = '<div id="' . $this->get_name() . '_action_bar_left" class="action_bar_left">';
        $html[] = '<h3>' . Translation :: get('ActionBar') . '</h3>';

        $hasSearchForm = ! is_null($this->searchForm);
        $hasLeftItems = (count($leftItems) > 0);
        $hasMiddleItems = (count($middleItems) > 0);
        $hasLeftAndMiddleItems = (count($leftItems) > 0) && (count($middleItems) > 0);

        if (! is_null($this->searchForm))
        {
            $searchForm = $this->searchForm;
            $html[] = $searchForm->as_html();
        }

        if ($hasSearchForm && ($hasLeftItems || $hasMiddleItems))
        {
            $html[] = '<div class="divider"></div>';
        }

        if ($hasLeftItems)
        {
            $html[] = '<div class="clear"></div>';

            $toolbar = new Toolbar();
            $toolbar->set_items($leftItems);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }

        if ($hasLeftAndMiddleItems)
        {
            $html[] = '<div class="divider"></div>';
        }

        if ($hasMiddleItems)
        {
            $html[] = '<div class="clear"></div>';

            $toolbar = new Toolbar();
            $toolbar->set_items($middleItems);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }

        $html[] = '<div class="clear"></div>';

        $html[] = '<div id="' . $this->get_name() .
             '_action_bar_left_hide_container" class="action_bar_left_hide_container hide">';
        $html[] = '<a id="' . $this->get_name() .
             '_action_bar_left_hide" class="action_bar_left_hide" href="#"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/ActionBar/Hide') . '" /></a>';
        $html[] = '<a id="' . $this->get_name() .
             '_action_bar_left_show" class="action_bar_left_show" href="#"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/ActionBar/Show') . '" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'ActionBarVertical.js');

        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
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
