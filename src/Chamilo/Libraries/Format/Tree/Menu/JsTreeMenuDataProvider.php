<?php
namespace Chamilo\Libraries\Format\Tree\Menu;

use stdClass;

/**
 * @package Chamilo\Libraries\Format\Tree\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JsTreeMenuDataProvider
{
    protected TreeMenuDataProvider $treeMenuDataProvider;

    public function __construct(TreeMenuDataProvider $treeMenuDataProvider)
    {
        $this->treeMenuDataProvider = $treeMenuDataProvider;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tree\TreeNode[] $treeNodes
     */
    protected function convertTreeNodes(array $treeNodes): array
    {
        $jsonNodes = [];

        foreach ($treeNodes as $treeNode)
        {
            $jsonNode = new stdClass();
            $jsonNode->id = $treeNode->getIdentifier();
            $jsonNode->text = $treeNode->getText();
            $jsonNode->a_attr = $treeNode->getAnchorAttributes();

            if (count($treeNode->getChildNodes()) > 0)
            {
                $jsonNode->children = $this->convertTreeNodes($treeNode->getChildNodes());
            }
            elseif ($treeNode->getHasChildNodes())
            {
                $jsonNode->children = $treeNode->getHasChildNodes();
            }

            $jsonNodes[] = $jsonNode;
        }

        return $jsonNodes;
    }

    public function getData(string $uriFormat, ?string $itemIdentifier): array
    {
        $data = $this->getTreeMenuDataProvider()->getData($uriFormat, $itemIdentifier);

        return $this->convertTreeNodes($data);
    }

    public function getTreeMenuDataProvider(): TreeMenuDataProvider
    {
        return $this->treeMenuDataProvider;
    }
}
