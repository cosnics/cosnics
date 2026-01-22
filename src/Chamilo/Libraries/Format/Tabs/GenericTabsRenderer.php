<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GenericTabsRenderer
{
    public const PARAM_SELECTED_TAB = 'tab';

    private ChamiloRequest $request;

    public function __construct(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     */
    protected function getSelectedTab(string $name, TabsCollection $tabs): ?string
    {
        try
        {
            $selectedTabs = $this->getRequest()->query->all(self::PARAM_SELECTED_TAB);
            $selectedTab = $selectedTabs[$name];

            if (!is_null($selectedTab) && $tabs->isValidIdentifier($selectedTab))
            {
                return $selectedTab;
            }

            return null;
        }
        catch (BadRequestException)
        {
            return null;
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     */
    public function renderFooter(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = '</div>';

        $html[] = '<script>';
        $html[] = '$(\'#' . $name . 'Tabs a\').click(function (e) {
  e.preventDefault()
  $(this).tab(\'show\')
})';

        $selectedTab = $this->getSelectedTab($name, $tabs);

        if (isset($selectedTab))
        {
            $html[] = '$(\'#' . $name . 'Tabs a[href="#' . $name . '-' . $selectedTab . '"]\').tab(\'show\');';
        }
        else
        {
            $html[] = '$(\'#' . $name . 'Tabs a:first\').tab(\'show\')';
        }

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function renderHeaderBottom(string $name): string
    {
        $html = [];

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '<div id="' . $name . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }

    public function renderHeaderTop(string $name): string
    {
        $html = [];

        $html[] = '<div id="' . $name . 'Tabs">';
        $html[] = '<ul class="nav nav-tabs tabs-header dynamic-visual-tabs">';

        return implode(PHP_EOL, $html);
    }
}