<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Chamilo\Libraries\Format\Structure\Toolbar;

/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions and a right
 * menu for a search bar.
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 */
class ActionBarRenderer
{
    // Action bar sections
    const ACTION_BAR_COMMON = 'common';
    const ACTION_BAR_TOOL = 'tool';
    const ACTION_BAR_SEARCH = 'search';

    // Types
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_VERTICAL = 'vertical';

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[][]
     */
    private $actions = array(
        self::ACTION_BAR_COMMON => array(),
        self::ACTION_BAR_TOOL => array(),
        self::ACTION_BAR_SEARCH => array());

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm
     */
    private $searchForm;

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

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
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
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     */
    public function add_action($type = self :: ACTION_BAR_COMMON, $action)
    {
        $this->actions[$type][] = $action;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     */
    public function add_common_action($action)
    {
        $this->actions[self::ACTION_BAR_COMMON][] = $action;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $action
     */
    public function add_tool_action($action)
    {
        $this->actions[self::ACTION_BAR_TOOL][] = $action;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_tool_actions()
    {
        return $this->actions[self::ACTION_BAR_TOOL];
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function get_common_actions()
    {
        return $this->actions[self::ACTION_BAR_COMMON];
    }

    /**
     *
     * @return string
     */
    public function get_search_url()
    {
        return $this->actions[self::ACTION_BAR_SEARCH];
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $actions
     */
    public function set_tool_actions($actions)
    {
        $this->actions[self::ACTION_BAR_TOOL] = $actions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $actions
     */
    public function set_common_actions($actions)
    {
        $this->actions[self::ACTION_BAR_COMMON] = $actions;
    }

    /**
     *
     * @param string $search_url
     */
    public function set_search_url($search_url)
    {
        $this->actions[self::ACTION_BAR_SEARCH] = $search_url;
        $this->searchForm = new ActionBarSearchForm($search_url);
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $type = $this->type;

        switch ($type)
        {
            case self::TYPE_HORIZONTAL :
                return $this->renderHorizontal();
                break;
            case self::TYPE_VERTICAL :
                return $this->renderVertical();
                break;
            default :
                return $this->renderHorizontal();
                break;
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
     * @return string
     */
    public function renderHorizontal()
    {
        $common_actions = $this->get_common_actions();
        $tool_actions = $this->get_tool_actions();

        if (count($common_actions) == 0 && count($tool_actions) == 0 && is_null($this->searchForm))
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

        if ($common_actions && count($common_actions) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($common_actions);
            $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
            $html[] = $toolbar->as_html();
        }
        $html[] = '</td>';

        $html[] = '<td class="tool_menu split split_bevel">';

        if ($tool_actions && count($tool_actions) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($tool_actions);
            $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
            $html[] = $toolbar->as_html();
        }

        $html[] = '</td>';

        $html[] = '<td class="search_menu split_bevel">';
        if (! is_null($this->searchForm))
        {
            $search_form = $this->searchForm;
            if ($search_form)
            {
                if ($search_form->validate())
                {
                    if ($this->clear_form_submitted())
                    {
                        $redirect_response = new RedirectResponse($this->get_search_url());
                        $redirect_response->send();
                    }
                }
                $html[] = '<div class="search_form">';
                $html[] = $search_form->as_html();
                $html[] = '</div>';
            }
        }

        $html[] = '</td>';

        $html[] = '</tr>';
        $html[] = '</table>';

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return boolean
     */
    public function clear_form_submitted()
    {
        return ! is_null(Request::post('clear'));
    }

    /**
     *
     * @return string
     */
    public function renderVertical()
    {
        $common_actions = $this->get_common_actions();
        $tool_actions = $this->get_tool_actions();

        if (count($common_actions) == 0 && count($tool_actions) == 0 && is_null($this->searchForm))
        {
            return '';
        }

        $html = array();

        $html[] = '<div id="' . $this->get_name() . '_action_bar_left" class="action_bar_left">';
        $html[] = '<h3>' . Translation::get('ActionBar') . '</h3>';

        $action_bar_has_search_form = ! is_null($this->searchForm);
        $action_bar_has_common_actions = (count($common_actions) > 0);
        $action_bar_has_tool_actions = (count($tool_actions) > 0);
        $action_bar_has_common_and_tool_actions = (count($common_actions) > 0) && (count($tool_actions) > 0);

        if (! is_null($this->searchForm))
        {
            $search_form = $this->searchForm;
            $html[] = $search_form->as_html();
        }

        if ($action_bar_has_search_form && ($action_bar_has_common_actions || $action_bar_has_tool_actions))
        {
            $html[] = '<div class="divider"></div>';
        }

        if ($action_bar_has_common_actions)
        {
            $html[] = '<div class="clear"></div>';

            $toolbar = new Toolbar();
            $toolbar->set_items($common_actions);
            $toolbar->set_type(Toolbar::TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }

        if ($action_bar_has_common_and_tool_actions)
        {
            $html[] = '<div class="divider"></div>';
        }

        if ($action_bar_has_tool_actions)
        {
            $html[] = '<div class="clear"></div>';

            $toolbar = new Toolbar();
            $toolbar->set_items($tool_actions);
            $toolbar->set_type(Toolbar::TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }

        $html[] = '<div class="clear"></div>';

        $html[] = '<div id="' . $this->get_name() .
             '_action_bar_left_hide_container" class="action_bar_left_hide_container hide">';
        $html[] = '<a id="' . $this->get_name() .
             '_action_bar_left_hide" class="action_bar_left_hide" href="#"><img src="' .
             Theme::getInstance()->getCommonImagePath('Action/ActionBar/Hide') . '" /></a>';
        $html[] = '<a id="' . $this->get_name() .
             '_action_bar_left_show" class="action_bar_left_show" href="#"><img src="' .
             Theme::getInstance()->getCommonImagePath('Action/ActionBar/Show') . '" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'ActionBarVertical.js');

        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
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
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $properties
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
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
            $search_conditions = Utilities::query_to_condition($query, $properties);

            $condition = $search_conditions;
        }
        return $condition;
    }
}
