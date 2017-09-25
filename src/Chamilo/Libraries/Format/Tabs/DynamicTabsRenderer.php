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

    public function hasTabs()
    {
        return count($this->get_tabs()) > 0;
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
        $tab->set_id($this->name . '-' . $tab->get_id());
        $this->tabs[] = $tab;
    }

    public function header()
    {
        $tabs = $this->get_tabs();

        $html = array();

        $html[] = '<div id="' . $this->name . 'Tabs">';

        // Tab headers
        $html[] = '<ul class="nav nav-tabs tabs-header dynamic-visual-tabs">';
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->header();
        }
        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '<div id="' . $this->name . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }

    public function get_selected_tab()
    {
        $selected_tabs = Request::get(self::PARAM_SELECTED_TAB);

        if (! is_array($selected_tabs) && ! empty($selected_tabs))
        {
            $selected_tab = $selected_tabs;
        }

        $selected_tab = $selected_tabs[$this->get_name()];

        if (! is_null($selected_tab))
        {
            if (! $this->is_tab_active($selected_tab))
            {
                return null;
            }

            return $this->get_name() . '-' . $selected_tab;
        }
        else
        {
            return null;
        }
    }

    /**
     * Checks if a tab is active in this tabs renderer by a given tab name
     *
     * @param string $tab_name
     *
     * @return bool
     */
    protected function is_tab_name_active($tab_name)
    {
        foreach ($this->get_tabs() as $tab)
        {
            if ($tab->get_name() == $tab_name)
            {
                return true;
            }
        }

        return false;
    }

    protected function is_tab_active($tabIdentifier)
    {
        $tabId = $this->get_name() . '-' . $tabIdentifier;

        foreach ($this->get_tabs() as $tab)
        {
            if ($tab->get_id() == $tabId)
            {
                return true;
            }
        }

        return false;
    }

    public function footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '<script type="text/javascript">';

        $html[] = '$(\'#' . $this->name . 'Tabs a\').click(function (e) {
  e.preventDefault()
  $(this).tab(\'show\')
})';

        $selected_tab = $this->get_selected_tab();

        if (isset($selected_tab))
        {
            $html[] = '$(\'#' . $this->name . 'Tabs a[href="#' . $selected_tab . '"]\').tab(\'show\');';
        }
        else
        {
            $html[] = '$(\'#' . $this->name . 'Tabs a:first\').tab(\'show\')';
        }

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        if ($this->hasTabs())
        {
            $html = array();
            $html[] = $this->header();

            // Tab content
            $tabs = $this->get_tabs();

            foreach ($tabs as $key => $tab)
            {
                $html[] = $tab->body($this->name . '-' . $tab->get_id());
            }

            $html[] = $this->footer();

            return implode(PHP_EOL, $html);
        }
    }
}
