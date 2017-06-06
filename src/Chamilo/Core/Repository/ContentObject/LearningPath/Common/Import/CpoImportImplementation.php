<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

class CpoImportImplementation extends ImportImplementation
{

    public function import()
    {
        return ContentObjectImport::launch($this);
    }

    public function post_import($contentObject)
    {
        $contentObjectNode = $this->get_content_object_import_parameters()->get_content_object_node();

        $treeNodeData = array();

        /** @var \DOMNodeList $childNodes */
        $childNodes = $this->get_controller()->get_dom_xpath()->query('children/child', $contentObjectNode);
        foreach ($childNodes as $key => $childNode)
        {
            $properties = array();

            /** @var \DOMElement $childNode */

            foreach ($childNode->childNodes as $treeNodeDataPropertyNode)
            {
                /** @var \DOMElement $treeNodeDataPropertyNode */
                if ($treeNodeDataPropertyNode->nodeType == XML_TEXT_NODE)
                {
                    continue;
                }

                $properties[$treeNodeDataPropertyNode->nodeName] = $treeNodeDataPropertyNode->nodeValue;
            }

            $treeNodeData = new TreeNodeData($properties);
            $treeNodeData->setLearningPathId((int) $contentObject->getId());
            $treeNodeData[] = $treeNodeData;
        }

        $this->importTreeNodesData($treeNodeData);

        return $contentObject;
    }

    /**
     * Imports the learning path children
     *
     * @param TreeNodeData[] $treeNodeData
     */
    protected function importTreeNodesData($treeNodeData)
    {
        $orderedTreeNodesData =
            $this->orderTreeNodesDataPerParentTreeNodeDataId($treeNodeData);

        $this->importTreeNodesDataForParent($orderedTreeNodesData);
    }

    /**
     * Imports the learning path children for a given parent.
     *
     * @param TreeNodeData[][] $orderedTreeNodesData
     * @param TreeNodeData|null $parentTreeNodeData
     * @param int $oldParentId
     */
    protected function importTreeNodesDataForParent(
        $orderedTreeNodesData, TreeNodeData $parentTreeNodeData = null, $oldParentId = 0
    )
    {
        $treeNodesData = $orderedTreeNodesData[$oldParentId];
        foreach ($treeNodesData as $treeNodeData)
        {
            $newContentObjectId =
                $this->get_controller()->get_content_object_id_cache_id($treeNodeData->getContentObjectId());

            if (empty($newContentObjectId))
            {
                $content_object_node_list = $this->get_controller()->get_dom_xpath()->query(
                    '/export/content_objects/content_object[@id="' . $treeNodeData->getContentObjectId() . '"]'
                );

                if ($content_object_node_list->length == 1)
                {
                    $this->get_controller()->process_content_object($content_object_node_list->item(0));

                    $newContentObjectId = $this->get_controller()->get_content_object_id_cache_id(
                        $treeNodeData->getContentObjectId()
                    );
                }
                else
                {
                    continue;
                }
            }

            $oldTreeNodeDataId = $treeNodeData->getId();

            $treeNodeData->setId(null);
            $treeNodeData->setContentObjectId((int) $newContentObjectId);

            $treeNodeData->setParentTreeNodeDataId(
                is_null($parentTreeNodeData) ? 0 : (int) $parentTreeNodeData->getId()
            );

            $treeNodeData->create();

            $this->importTreeNodesDataForParent(
                $orderedTreeNodesData, $treeNodeData, $oldTreeNodeDataId
            );
        }
    }

    /**
     * Orders the learning path children by the parent learning path child id
     *
     * @param TreeNodeData[] $treeNodesData
     *
     * @return TreeNodeData[][]
     */

    protected function orderTreeNodesDataPerParentTreeNodeDataId($treeNodesData)
    {
        $orderedTreeNodesData = array();

        foreach ($treeNodesData as $treeNodeData)
        {
            $orderedTreeNodesData[$treeNodeData->getParentTreeNodeDataId()]
                [$treeNodeData->getDisplayOrder()] = $treeNodeData;
        }

        return $orderedTreeNodesData;
    }
}
