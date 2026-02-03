<?php
namespace Chamilo\Libraries\UserInterface\Tree\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\Tree\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class TreeMenuDataProvider
{
    protected function __getData(
        string $uriFormat, ?string $identifier, Closure $getIdentifier, Closure $getText, Closure $hasChildNodes
    ): array
    {
        if (!$identifier) {
            $rootDataClass = $this->getRootDataClass();
            $identifier = $getIdentifier($rootDataClass);

            return [
                $this->getTreeNode(
                    uriFormat: $uriFormat, identifier: $identifier, text: $getText($rootDataClass),
                    childNodes: $this->processChildren(
                        $uriFormat, $identifier, $getIdentifier, $getText, $hasChildNodes
                    )
                )
            ];
        }
        else {
            return $this->processChildren($uriFormat, $identifier, $getIdentifier, $getText, $hasChildNodes);
        }
    }

    abstract protected function getChildDataClasses(string $parentIdentifier): ArrayCollection;

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    abstract public function getData(string $uriFormat, ?string $identifier): array;

    abstract protected function getRootDataClass(): DataClass;

    protected function getTreeNode(
        string $uriFormat, string $identifier, string $text, array $childNodes = [], bool $hasChildNodes = false
    ): TreeNode
    {
        return new TreeNode(identifier: $identifier, text: $text, anchorAttributes: [
            'href' => html_entity_decode(
                $this->getTreeNodeUri($uriFormat, $identifier)
            )
        ], childNodes: $childNodes, hasChildNodes: $hasChildNodes);
    }

    protected function getTreeNodeUri(string $uriFormat, string $selectedItemIdentifier): string
    {
        return htmlentities(sprintf($uriFormat, $selectedItemIdentifier));
    }

    protected function processChildren(
        string $uriFormat, string $parentIdentifier, Closure $getIdentifier, Closure $getText, Closure $hasChildNodes
    ): array
    {
        $childDataClasses = $this->getChildDataClasses($parentIdentifier);
        $childTreeNodes = [];

        foreach ($childDataClasses as $childDataClass) {
            $childTreeNodes[] = $this->getTreeNode(
                uriFormat: $uriFormat, identifier: $getIdentifier($childDataClass), text: $getText($childDataClass),
                hasChildNodes: $hasChildNodes($childDataClass)
            );
        }

        return $childTreeNodes;
    }
}
