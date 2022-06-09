<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabsRenderer
{
    public const PARAM_SELECTED_TAB = 'tab';

    private string $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\Tab[]
     */
    private array $tabs;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->tabs = [];
    }

    public function render(): string
    {
        $html = [];

        if ($this->hasTabs())
        {
            $html[] = $this->header();

            // Tab content
            $tabs = $this->getTabs();

            foreach ($tabs as $tab)
            {
                $html[] = $tab->body($this->getName() . '-' . $tab->getIdentifier());
            }

            $html[] = $this->footer();
        }

        return implode(PHP_EOL, $html);
    }

    public function addTab(Tab $tab)
    {
        $tab->setIdentifier($this->name . '-' . $tab->getIdentifier());
        $this->tabs[] = $tab;
    }

    public function footer(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '<script>';

        $html[] = '$(\'#' . $this->getName() . 'Tabs a\').click(function (e) {
  e.preventDefault()
  $(this).tab(\'show\')
})';

        $selected_tab = $this->getSelectedTab();

        if (isset($selected_tab))
        {
            $html[] = '$(\'#' . $this->getName() . 'Tabs a[href="#' . $selected_tab . '"]\').tab(\'show\');';
        }
        else
        {
            $html[] = '$(\'#' . $this->getName() . 'Tabs a:first\').tab(\'show\')';
        }

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function set_name(string $name)
    {
        $this->name = $name;
    }

    public function getSelectedTab(): ?string
    {
        $selectedTabs = Request::get($this->getSelectedTabVariableName());

        if (!is_array($selectedTabs) && !empty($selectedTabs))
        {
            $selectedTabs = [$selectedTabs];
        }

        $selectedTab = $selectedTabs[$this->getName()];

        if (!is_null($selectedTab))
        {
            if (!$this->isTabActive($selectedTab))
            {
                return null;
            }

            return $this->getName() . '-' . $selectedTab;
        }
        else
        {
            return null;
        }
    }

    protected function getSelectedTabVariableName(): string
    {
        return self:: PARAM_SELECTED_TAB;
    }

    /**
     * @return \Chamilo\Libraries\Format\Tabs\Tab[]
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function hasTabs(): bool
    {
        return count($this->getTabs()) > 0;
    }

    /**
     *
     * @return string
     */
    public function header(): string
    {
        $tabs = $this->getTabs();

        $html = [];

        $html[] = '<div id="' . $this->getName() . 'Tabs">';

        // Tab headers
        $html[] = '<ul class="nav nav-tabs tabs-header dynamic-visual-tabs">';

        foreach ($tabs as $tab)
        {
            $html[] = $tab->header();
        }

        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '<div id="' . $this->getName() . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }

    protected function isTabActive(string $tabIdentifier): bool
    {
        $tabId = $this->getName() . '-' . $tabIdentifier;

        foreach ($this->getTabs() as $tab)
        {
            if ($tab->getIdentifier() == $tabId)
            {
                return true;
            }
        }

        return false;
    }

    public function size(): int
    {
        return count($this->tabs);
    }

}
