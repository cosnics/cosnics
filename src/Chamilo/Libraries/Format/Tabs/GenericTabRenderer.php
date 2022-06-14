<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GenericTabRenderer
{
    public function renderContentFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = $this->renderContentFooterForList();

        return implode(PHP_EOL, $html);
    }

    public function renderContentFooterForList(): string
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderContentHeader(string $tabsRendererName, GenericTab $tab): string
    {
        $html = [];

        $html[] = $this->renderContentHeaderForList($tabsRendererName, $tab);
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
    }

    public function renderContentHeaderForList(string $tabsRendererName, GenericTab $tab): string
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $tabsRendererName . '-' . $tab->getIdentifier() . '">';
        $html[] = '<div class="list-group">';

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(string $tabsRendererName, GenericTab $tab): string
    {
        $html = [];
        $html[] = '<li>';
        $html[] = '<a title="' . htmlentities(strip_tags($tab->getLabel())) . '" href="#' . $tabsRendererName . '-' .
            $tab->getIdentifier() . '">';
        $html[] = '<span class="category">';

        if ($tab->getInlineGlyph() && $tab->isIconVisible())
        {
            $html[] = $tab->getInlineGlyph()->render();
        }

        if ($tab->getLabel() && $tab->isTextVisible())
        {
            $html[] = '<span class="title">' . $tab->getLabel() . '</span>';
        }

        $html[] = '</span>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}
