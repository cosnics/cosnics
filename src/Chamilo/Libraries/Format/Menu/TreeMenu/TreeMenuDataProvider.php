<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
abstract class TreeMenuDataProvider
{
    abstract public function getData(string $uriFormat, ?string $itemIdentifier): array;

    protected function getTreeNode(
        string $uriFormat, string $identifier, string $text, array $childNodes = [], bool $hasChildNodes = false
    ): TreeNode
    {
        $item = new TreeNode($identifier, $text);

        $item->setAnchorAttributes(['href' => html_entity_decode($this->getTreeNodeUri($uriFormat, $identifier))]);

        if (count($childNodes) > 0)
        {
            $item->setChildNodes($childNodes);
        }
        else
        {
            $item->setHasChildNodes($hasChildNodes);
        }

        return $item;
    }

    protected function getTreeNodeUri(string $uriFormat, string $selectedItemIdentifier): string
    {
        return htmlentities(sprintf($uriFormat, $selectedItemIdentifier));
    }
}
