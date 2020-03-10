<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImportParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\LevelJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

class CpoImportImplementation extends ImportImplementation
{
    use DependencyInjectionContainerTrait;

    public function import()
    {
        return ContentObjectImport::launch($this);
    }

    public function post_import(Rubric $contentObject)
    {
        $this->initializeContainer();

        /** @var CpoContentObjectImportParameters $importParameters */
        $importParameters = $this->get_content_object_import_parameters();

        /** @var CpoContentObjectImportController $controller */
        $controller = $this->get_controller();

        $contentObjectNode = $importParameters->get_content_object_node();
        $rubricDataElementList = $controller->get_dom_xpath()->query('rubric-data', $contentObjectNode);
        $rubricDataJSON = $rubricDataElementList->item(0)->textContent;

        $rubricDataArray = $this->getSerializer()->deserialize($rubricDataJSON, 'array', 'json');

        $rubricData = new RubricData($rubricDataArray['root_node']['title']);
        $rubricData->setUseScores($rubricDataArray['use_scores']);
        $rubricData->setContentObjectId($contentObject->getId());

        $this->createLevels($rubricData, $rubricDataArray['levels']);

        $this->createTreeNodeChildren(
            $rubricData->getRootNode(), $rubricData, $rubricDataArray['root_node']['children']
        );

        var_dump($rubricData->getLevels());
        exit;

        return $contentObject;
    }

    /**
     * @param TreeNode $parentNode
     * @param RubricData $rubricData
     * @param array $treeNodesArray
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    protected function createTreeNodeChildren(TreeNode $parentNode, RubricData $rubricData, array $treeNodesArray)
    {
        foreach ($treeNodesArray as $treeNodeArray)
        {
            $treeNodeJSON = json_encode($treeNodeArray);
            /** @var TreeNodeJSONModel $treeNodeJSONModel */
            $treeNodeJSONModel = $this->getSerializer()->deserialize($treeNodeJSON, TreeNodeJSONModel::class, 'json');

            $treeNode = $treeNodeJSONModel->toTreeNode($rubricData);

            if($treeNode instanceof CriteriumNode)
            {
                var_dump($treeNodeJSON['choices']);
            }

            $parentNode->addChild($treeNode);

            if (array_key_exists('children', $treeNodeArray))
            {
                $childrenArray = $treeNodeArray['children'];
                if (!empty($children))
                {
                    $this->createTreeNodeChildren($treeNode, $rubricData, $childrenArray);
                }
            }
        }
    }

    /**
     * @param RubricData $rubricData
     * @param array $levelsArray
     */
    protected function createLevels(RubricData $rubricData, array $levelsArray)
    {
        foreach($levelsArray as $levelArray)
        {
            $levelJSON = json_encode($levelArray);

            /** @var LevelJSONModel $levelJSONModel */
            $levelJSONModel = $this->getSerializer()->deserialize($levelJSON, LevelJSONModel::class, 'json');
            $levelJSONModel->toLevel($rubricData);
        }
    }

    /**
     * @return RubricService
     */
    public function getRubricService()
    {
        return $this->getService(RubricService::class);
    }

}
