<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;

class CpoImportImplementation extends ImportImplementation
{

    public function import()
    {
        return ContentObjectImport::launch($this);
    }

    public function post_import($contentObject)
    {
        $contentObjectNode = $this->get_content_object_import_parameters()->get_content_object_node();

        $learningPathChildren = array();

        /** @var \DOMNodeList $childNodes */
        $childNodes = $this->get_controller()->get_dom_xpath()->query('children/child', $contentObjectNode);
        foreach ($childNodes as $key => $childNode)
        {
            $properties = array();

            /** @var \DOMElement $childNode */

            foreach ($childNode->childNodes as $learningPathChildPropertyNode)
            {
                /** @var \DOMElement $learningPathChildPropertyNode */
                if ($learningPathChildPropertyNode->nodeType == XML_TEXT_NODE)
                {
                    continue;
                }

                $properties[$learningPathChildPropertyNode->nodeName] = $learningPathChildPropertyNode->nodeValue;
            }

            $learningPathChild = new LearningPathChild($properties);
            $learningPathChild->setLearningPathId((int) $contentObject->getId());
            $learningPathChildren[$childNode->getAttribute('id')] = $learningPathChild;
        }

        $this->importLearningPathChildren($learningPathChildren);

        return $contentObject;
    }

    /**
     * Imports the learning path children
     *
     * @param LearningPathChild[] $learningPathChildren
     */
    protected function importLearningPathChildren($learningPathChildren)
    {
        $orderedLearningPathChildren =
            $this->orderLearningPathChildrenPerParentLearningPathChildId($learningPathChildren);

        $this->importLearningPathChildrenForParent($orderedLearningPathChildren);
    }

    /**
     * Imports the learning path children for a given parent.
     *
     * @param LearningPathChild[][] $orderedLearningPathChildren
     * @param LearningPathChild|null $parentLearningPathChild
     * @param int $oldParentId
     */
    protected function importLearningPathChildrenForParent(
        $orderedLearningPathChildren, LearningPathChild $parentLearningPathChild = null, $oldParentId = 0
    )
    {
        $learningPathChildren = $orderedLearningPathChildren[$oldParentId];
        foreach ($learningPathChildren as $oldLearningPathChildId => $learningPathChild)
        {
            $newContentObjectId =
                $this->get_controller()->get_content_object_id_cache_id($learningPathChild->getContentObjectId());

            if (empty($newContentObjectId))
            {
                $content_object_node_list = $this->get_controller()->get_dom_xpath()->query(
                    '/export/content_objects/content_object[@id="' . $learningPathChild->getContentObjectId() . '"]'
                );

                if ($content_object_node_list->length == 1)
                {
                    $this->get_controller()->process_content_object($content_object_node_list->item(0));

                    $newContentObjectId = $this->get_controller()->get_content_object_id_cache_id(
                        $learningPathChild->getContentObjectId()
                    );
                }
                else
                {
                    continue;
                }
            }

            $learningPathChild->setContentObjectId((int) $newContentObjectId);

            $learningPathChild->setParentLearningPathChildId(
                is_null($parentLearningPathChild) ? 0 : (int) $parentLearningPathChild->getId()
            );

            $learningPathChild->create();

            $this->importLearningPathChildrenForParent(
                $orderedLearningPathChildren, $learningPathChild, $oldLearningPathChildId
            );
        }
    }

    /**
     * Orders the learning path children by the parent learning path child id
     *
     * @param LearningPathChild[] $learningPathChildren
     *
     * @return LearningPathChild[][]
     */

    protected function orderLearningPathChildrenPerParentLearningPathChildId($learningPathChildren)
    {
        $orderedLearningPathChildren = array();

        foreach ($learningPathChildren as $learningPathChildId => $learningPathChild)
        {
            $orderedLearningPathChildren[$learningPathChild->getParentLearningPathChildId()]
                [$learningPathChild->getDisplayOrder()] = $learningPathChild;
        }

        return $orderedLearningPathChildren;
    }
}
