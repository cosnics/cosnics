<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
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
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    protected function getSelectedTab(string $name, TabsCollection $tabs): ?string
    {
        try {
            $selectedTabs = $this->getRequest()->query->all(self::PARAM_SELECTED_TAB);
            $selectedTab = $selectedTabs[$name];

            if (!is_null($selectedTab) && $tabs->isValidIdentifier($selectedTab)) {
                return $selectedTab;
            }

            return null;
        }
        catch (BadRequestException) {
            return null;
        }
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
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

        if (isset($selectedTab)) {
            $html[] = '$(\'#' . $name . 'Tabs a[href="#' . $name . '-' . $selectedTab . '"]\').tab(\'show\');';
        }
        else {
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