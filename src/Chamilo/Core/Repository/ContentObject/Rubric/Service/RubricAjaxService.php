<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\TreeNodeJSONModel;
use JMS\Serializer\Serializer;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricAjaxService
{
    /**
     * @var RubricService
     */
    protected $rubricService;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * RubricAjaxService constructor.
     *
     * @param RubricService $rubricService
     */
    public function __construct(RubricService $rubricService)
    {
        $this->rubricService = $rubricService;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function addTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONModel)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONModel);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $parentTreeNode = $rubricData->getParentNodeById($treeNodeJSONModel->getParentId());
        $treeNode = $treeNodeJSONModel->toTreeNode($rubricData);
        $parentTreeNode->addChild($treeNode);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'tree_node' => $treeNode->toJSONModel()
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONModel)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONModel);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $parentTreeNode = $rubricData->getParentNodeById($treeNodeJSONModel->getParentId());
        $treeNode = $rubricData->getTreeNodeById($treeNodeJSONModel->getId());

        $parentTreeNode->removeChild($treeNode);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'tree_node' => $treeNode->toJSONModel()
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONModel)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONModel);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $treeNode = $rubricData->getTreeNodeById($treeNodeJSONModel->getId());

        $treeNode->updateFromJSONModel($treeNodeJSONModel);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'tree_node' => $treeNode->toJSONModel()
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONModel
     *
     * @param int $newParentId
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveTreeNode(
        int $rubricDataId, int $versionId, string $treeNodeJSONModel, int $newParentId, int $newSort
    )
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONModel);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $treeNode = $rubricData->getTreeNodeById($treeNodeJSONModel->getId());

        $newParentNode = $rubricData->getParentNodeById($newParentId);
        $treeNode->setParentNode($newParentNode);
        $newParentNode->moveChild($treeNode, $newSort);

        $treeNode->updateFromJSONModel($treeNodeJSONModel);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'tree_node' => $treeNode->toJSONModel()
        ];
    }

    /**
     * @param string $treeNodeData
     *
     * @return TreeNodeJSONModel
     */
    protected function parseTreeNodeData(string $treeNodeData)
    {
        $treeNodeData = $this->serializer->deserialize(
            $treeNodeData, TreeNodeJSONModel::class, 'json'
        );

        if (!$treeNodeData instanceof TreeNodeJSONModel)
        {
            throw new \RuntimeException('Could not parse the tree node JSON model');
        }

        return $treeNodeData;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function addLevel(int $rubricDataId, int $versionId, string $levelJSONModel)
    {

    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeLevel(int $rubricDataId, int $versionId, string $levelJSONModel)
    {

    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateLevel(int $rubricDataId, int $versionId, string $levelJSONModel)
    {

    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveLevel(int $rubricDataId, int $versionId, string $levelJSONModel)
    {

    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $choiceJSONModel
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateChoice(int $rubricDataId, int $versionId, string $choiceJSONModel)
    {

    }

}
