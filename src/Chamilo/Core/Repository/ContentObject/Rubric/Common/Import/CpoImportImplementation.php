<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImportParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\ChoiceJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\LevelJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

class CpoImportImplementation extends ImportImplementation
{
    use DependencyInjectionContainerTrait;

    /**
     * @var Level[]
     */
    protected $createdLevels = [];

    public function import()
    {
        return ContentObjectImport::launch($this);
    }

    /**
     * @param Rubric $contentObject
     *
     * @return Rubric
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function post_import(Rubric $contentObject)
    {
        $this->initializeContainer();

        $this->getRubricService()->deleteRubricData($contentObject->getActiveRubricDataId());

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

        $this->getRubricService()->saveRubric($rubricData);

        $contentObject->setActiveRubricDataId($rubricData->getId());
        $contentObject->update();

        return $contentObject;
    }

    /**
     * @param TreeNode $parentNode
     * @param RubricData $rubricData
     * @param array $treeNodesArray
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Exception
     */
    protected function createTreeNodeChildren(TreeNode $parentNode, RubricData $rubricData, array $treeNodesArray)
    {
        foreach ($treeNodesArray as $treeNodeArray)
        {
            $treeNodeJSON = json_encode($treeNodeArray);
            /** @var TreeNodeJSONModel $treeNodeJSONModel */
            $treeNodeJSONModel = $this->getSerializer()->deserialize($treeNodeJSON, TreeNodeJSONModel::class, 'json');

            $treeNode = $treeNodeJSONModel->toTreeNode($rubricData);

            if ($treeNode instanceof CriteriumNode)
            {
                $choicesArray = $treeNodeArray['choices'];

                // Clear all old choices because they were automatically created due to the new levels
                foreach($treeNode->getChoices() as $choice)
                {
                    $choice->setRubricData(null);
                    $choice->setLevel(null);
                    $choice->setCriterium(null);
                }

                foreach ($choicesArray as $choiceArray)
                {
                    $choiceJSON = json_encode($choiceArray);

                    /** @var ChoiceJSONModel $choiceJSONModel */
                    $choiceJSONModel =
                        $this->getSerializer()->deserialize($choiceJSON, ChoiceJSONModel::class, 'json');

                    $choice = $choiceJSONModel->toChoice($rubricData);

                    $level = $this->createdLevels[$choiceArray['level']['id']];

                    $choice->setLevel($level);
                    $treeNode->addChoice($choice);

                }
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
        foreach ($levelsArray as $levelArray)
        {
            $levelJSON = json_encode($levelArray);

            /** @var LevelJSONModel $levelJSONModel */
            $levelJSONModel = $this->getSerializer()->deserialize($levelJSON, LevelJSONModel::class, 'json');
            $level = $levelJSONModel->toLevel($rubricData);

            $this->createdLevels[$levelArray['id']] = $level;
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
