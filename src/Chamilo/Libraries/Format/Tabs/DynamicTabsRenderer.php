<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Platform\Session\Request;

class DynamicTabsRenderer
{
    const PARAM_SELECTED_TAB = 'tab';
    const TYPE_CONTENT = 1;
    const TYPE_ACTIONS = 2;

    private $name;

    private $tabs;

    public function __construct($name)
    {
        $this->name = $name;
        $this->tabs = array();
    }

    /**
     *
     * @return the $name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @return the $tabs
     */
    public function get_tabs()
    {
        return $this->tabs;
    }

    /**
     *
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param $tabs the $tabs to set
     */
    public function set_tabs($tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * Retrieves the number of tabs
     */
    public function size()
    {
        return count($this->tabs);
    }

    /**
     *
     * @param DynamicTab $tab
     */
    public function add_tab(DynamicTab $tab)
    {
        $tab->set_id($this->name . '_' . $tab->get_id());
        $this->tabs[] = $tab;
    }

    public function header()
    {
        $tabs = $this->get_tabs();

        $html = array();

        // $html[] = '<a name="top"></a>';
        $html[] = '<div id="' . $this->name . '_tabs">';

        // Tab headers
        $html[] = '<ul class="tabs-header">';
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->header();
        }
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    public function get_selected_tab()
    {
        $selected_tabs = Request :: get(self :: PARAM_SELECTED_TAB);

        // TODO: Added this for backwards compatibility from when the one request variable was shared between all
        // instances
        if (! is_array($selected_tabs) && ! empty($selected_tabs))
        {
            $selected_tab = $selected_tabs;
        }

        $selected_tab = $selected_tabs[$this->get_name()];

        if ($selected_tab)
        {
            return $this->get_name() . '_' . $selected_tab;
        }
        else
        {
            return null;
        }
    }

    public function footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'function setSearchTab(e, ui)
	{
		var searchForm = $("div.action_bar div.search_form form");
                var uri = searchForm.attr(\'action\');
                if(uri){
                    var url = $.query.load(uri);
                    var currentTabId = $("div.admin_tab:visible").attr(\'id\').replace("' .
             $this->get_name() . '_", "");
                    searchForm.attr(\'action\', url.set("tab", currentTabId).toString());
                }
	}';

        $html[] = '	$(\'#' . $this->get_name() . '_tabs > ul.tabs-header > li > a\').click(function(e) {
                e.preventDefault();
            });';

        $html[] = '	$("#' . $this->get_name() . '_tabs ul.tabs-header").css(\'display\', \'block\');';
        $html[] = '	$("#' . $this->get_name() . '_tabs h2").hide();';
        $html[] = '	$("#' . $this->get_name() . '_tabs").tabs();';
        $html[] = '	var tabs = $(\'#' . $this->get_name() . '_tabs\').tabs(\'paging\', { cycle: false, follow: false} );';

        $selected_tab = $this->get_selected_tab();
        if (isset($selected_tab))
        {
            $html[] = 'var activeTabIndex = $(\'#' . $this->get_name() . '_tabs > div\').index($("#' . $selected_tab .
                 '"))';
            $html[] = '	$(\'#' . $this->get_name() . '_tabs\').tabs( "option", "active", activeTabIndex );';
        }

        $html[] = '	$(document).on(\'tabsshow\', ("#' . $this->get_name() . '_tabs"), setSearchTab);';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        $html = array();
        $html[] = $this->header();

        // Tab content
        $tabs = $this->get_tabs();

        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->body($this->name . '_' . $tab->get_id());
        }

        $html[] = $this->footer();

        return implode(PHP_EOL, $html);
    }
}
