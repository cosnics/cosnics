<?php
namespace Chamilo\Libraries\UserInterface\Tree\Service;

use stdClass;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\UserInterface\Tree\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JsTreeMenuDataProvider
{
    public function __construct(protected TreeMenuDataProvider $treeMenuDataProvider)
    {
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[] $treeNodes
     */
    protected function convertTreeNodes(array $treeNodes): array
    {
        $jsonNodes = [];

        foreach ($treeNodes as $treeNode) {
            $jsonNode = new stdClass();
            $jsonNode->id = $treeNode->getIdentifier();
            $jsonNode->text = $treeNode->getText();
            $jsonNode->a_attr = $treeNode->getAnchorAttributes();

            if (count($treeNode->getChildNodes()) > 0) {
                $jsonNode->children = $this->convertTreeNodes($treeNode->getChildNodes());
            }
            elseif ($treeNode->getHasChildNodes()) {
                $jsonNode->children = $treeNode->getHasChildNodes();
            }

            $jsonNodes[] = $jsonNode;
        }

        return $jsonNodes;
    }

    public function getData(string $uriFormat, string|Uuid|null $itemIdentifier): array
    {
        $data = $this->treeMenuDataProvider->getData($uriFormat, $itemIdentifier);

        return $this->convertTreeNodes($data);
    }
}
