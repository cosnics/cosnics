<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentTabRenderer
{
    private GenericTabRenderer $genericTabRenderer;

    public function __construct(GenericTabRenderer $genericTabRenderer)
    {
        $this->genericTabRenderer = $genericTabRenderer;
    }

    public function getGenericTabRenderer(): GenericTabRenderer
    {
        return $this->genericTabRenderer;
    }

    public function renderContent(string $tabsRendererName, ContentTab $tab): string
    {
        $html = [];

        $html[] = $this->getGenericTabRenderer()->renderContentHeader($tabsRendererName, $tab);
        $html[] = $tab->getContent();
        $html[] = $this->getGenericTabRenderer()->renderContentFooter();

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(string $tabsRendererName, ContentTab $tab): string
    {
        return $this->getGenericTabRenderer()->renderNavigation($tabsRendererName, $tab);
    }
}