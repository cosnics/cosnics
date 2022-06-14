<?php
namespace Chamilo\Libraries\Format\Tabs\Link;

use Chamilo\Libraries\Format\Tabs\TabsCollection;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTabsRenderer
{
    private LinkTabRenderer $linkTabRenderer;

    public function __construct(LinkTabRenderer $linkTabRenderer)
    {
        $this->linkTabRenderer = $linkTabRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Link\LinkTab> $tabs
     * @param ?string $content
     *
     * @return string
     */
    public function render(TabsCollection $tabs, ?string $content = null): string
    {
        $html = [];

        $html[] = $this->renderHeader($tabs);

        if ($content)
        {
            $html[] = $content;
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getLinkTabRenderer(): LinkTabRenderer
    {
        return $this->linkTabRenderer;
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Link\LinkTab> $tabs
     *
     * @return string
     */
    public function renderHeader(TabsCollection $tabs): string
    {
        $html = [];

        $html[] = '<ul class="nav nav-tabs dynamic-visual-tabs">';

        foreach ($tabs as $tab)
        {
            $html[] = $this->getLinkTabRenderer()->renderNavigation($tab);
        }

        $html[] = '</ul>';
        $html[] = '<div class="dynamic-visual-tab-content">';
        $html[] = '<div class="list-group">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
    }
}
