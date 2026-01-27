<?php
namespace Chamilo\Libraries\UserInterface\Tree\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\Format\Tree\Options
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class OptionsTreeDataProvider
{

    protected function __getData(Closure $getIdentifier, Closure $getText, ?string $identifier): TreeNode
    {
        if (!$identifier)
        {
            $dataClass = $this->getRootDataClass();
        }
        else
        {
            $dataClass = $this->getDataClassByIdentifier($identifier);
        }

        $identifier = $getIdentifier($dataClass);

        return $this->getTreeNode(
            $identifier, $getText($dataClass), $this->processChildren($getIdentifier, $getText, $identifier)
        );
    }

    abstract protected function getChildDataClasses(string $parentIdentifier): ArrayCollection;

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    abstract public function getData(?string $identifier): array;

    abstract protected function getDataClassByIdentifier(string $identifier): DataClass;

    abstract protected function getRootDataClass(): DataClass;

    protected function getTreeNode(string $identifier, string $text, array $childNodes = []): TreeNode
    {
        return new TreeNode(identifier: $identifier, text: $text, childNodes: $childNodes);
    }

    protected function processChildren(Closure $getIdentifier, Closure $getText, string $parentIdentifier): array
    {
        $childDataClasses = $this->getChildDataClasses($parentIdentifier);
        $childTreeNodes = [];

        foreach ($childDataClasses as $childDataClass)
        {
            $childIdentifier = $getIdentifier($childDataClass);

            $childTreeNodes[] = $this->getTreeNode(
                $childIdentifier, $getText($childDataClass),
                $this->processChildren($getIdentifier, $getText, $childIdentifier)
            );
        }

        return $childTreeNodes;
    }
}
