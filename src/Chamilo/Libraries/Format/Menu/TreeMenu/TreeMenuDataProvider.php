<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use stdClass;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
abstract class TreeMenuDataProvider
{
    public function formatUrl(string $urlFormat, string $selectedItemIdentifier): string
    {
        return htmlentities(sprintf($urlFormat, $selectedItemIdentifier));
    }

    abstract public function getData(string $urlFormat, ?string $itemIdentifier): array;

    public function getTreeMenuItemUrl(string $urlFormat, string $selectedItemIdentifier): string
    {
        return $this->formatUrl($urlFormat, $selectedItemIdentifier);
    }
}
