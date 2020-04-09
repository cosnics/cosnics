<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicTabsRenderer
{
    const PARAM_SELECTED_TAB = 'tab';

    const TYPE_ACTIONS = 2;

    const TYPE_CONTENT = 1;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicTab[]
     */
    private $tabs;

    /**
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->tabs = array();
    }

    /**
     *
     * @return string
     */
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

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicTab $tab
     */
    public function add_tab(DynamicTab $tab)
    {
        $tab->set_id($this->name . '-' . $tab->get_id());
        $this->tabs[] = $tab;
    }

    /**
     *
     * @return string
     */
    public function footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '<script>';

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
    public function get_selected_tab()
    {
        $selectedTabs = Request::get(self::PARAM_SELECTED_TAB);

        if (!is_array($selectedTabs) && !empty($selectedTabs))
        {
            $selectedTabs = array($selectedTabs);
        }

        $selectedTab = $selectedTabs[$this->get_name()];

        if (!is_null($selectedTab))
        {
            if (!$this->is_tab_active($selectedTab))
            {
                return null;
            }

            return $this->get_name() . '-' . $selectedTab;
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicTab[]
     */
    public function get_tabs()
    {
        return $this->tabs;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicTab[] $tabs
     */
    public function set_tabs($tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     *
     * @return boolean
     */
    public function hasTabs()
    {
        return count($this->get_tabs()) > 0;
    }

    /**
     *
     * @return string
     */
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

    /**
     *
     * @param string $tabIdentifier
     *
     * @return boolean
     */
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

    /**
     * Checks if a tab is active in this tabs renderer by a given tab name
     *
     * @param string $tabName
     *
     * @return boolean
     */
    protected function is_tab_name_active($tabName)
    {
        foreach ($this->get_tabs() as $tab)
        {
            if ($tab->get_name() == $tabName)
            {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @return integer
     */
    public function size()
    {
        return count($this->tabs);
    }
}
