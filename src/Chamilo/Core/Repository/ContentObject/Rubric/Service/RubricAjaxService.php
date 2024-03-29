<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\ChoiceJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\LevelJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\RubricJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
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
     * @param Serializer $serializer
     */
    public function __construct(RubricService $rubricService, Serializer $serializer)
    {
        $this->rubricService = $rubricService;
        $this->serializer = $serializer;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONData
     * @param int $parentId
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function addTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONData, int $parentId)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONData);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $parentTreeNode = $rubricData->getParentNodeById($parentId);
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
     * @param string $treeNodeJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function removeTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONData)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONData);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $treeNode = $rubricData->getTreeNodeById($treeNodeJSONModel->getId());

        $rubricData->removeTreeNode($treeNode);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()]
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $treeNodeJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function updateTreeNode(int $rubricDataId, int $versionId, string $treeNodeJSONData)
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONData);

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
     * @param string $treeNodeJSONData
     *
     * @param int $newParentId
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function moveTreeNode(
        int $rubricDataId, int $versionId, string $treeNodeJSONData, int $newParentId, int $newSort
    )
    {
        $treeNodeJSONModel = $this->parseTreeNodeData($treeNodeJSONData);

        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $treeNode = $rubricData->getTreeNodeById($treeNodeJSONModel->getId());

        $newParentNode = $rubricData->getParentNodeById($newParentId);
        $treeNode->setParentNode($newParentNode);
        $newParentNode->moveChild($treeNode, $newSort);

//        $treeNode->updateFromJSONModel($treeNodeJSONModel);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'tree_node' => $treeNode->toJSONModel()
        ];
    }

    /**
     * @param string $treeNodeJSONData
     *
     * @return TreeNodeJSONModel
     */
    protected function parseTreeNodeData(string $treeNodeJSONData)
    {
        $treeNodeJSONModel = $this->serializer->deserialize(
            $treeNodeJSONData, TreeNodeJSONModel::class, 'json'
        );

        if (!$treeNodeJSONModel instanceof TreeNodeJSONModel)
        {
            throw new \RuntimeException('Could not parse the tree node JSON data');
        }

        return $treeNodeJSONModel;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function addLevel(int $rubricDataId, int $versionId, string $levelJSONData)
    {
        $levelJSONModel = $this->parseLevelJSONModel($levelJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $level = $levelJSONModel->toLevel($rubricData);
        $rubricData->addLevel($level);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'level' => LevelJSONModel::fromLevel($level)
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function removeLevel(int $rubricDataId, int $versionId, string $levelJSONData)
    {
        $levelJSONModel = $this->parseLevelJSONModel($levelJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $level = $rubricData->getLevelById($levelJSONModel->getId());
        $rubricData->removeLevel($level);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()]
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function updateLevel(int $rubricDataId, int $versionId, string $levelJSONData)
    {
        $levelJSONModel = $this->parseLevelJSONModel($levelJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $level = $rubricData->getLevelById($levelJSONModel->getId());
        $levelJSONModel->updateLevel($level);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'level' => LevelJSONModel::fromLevel($level)
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $levelJSONData
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function moveLevel(int $rubricDataId, int $versionId, string $levelJSONData, int $newSort)
    {
        $levelJSONModel = $this->parseLevelJSONModel($levelJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $level = $rubricData->getLevelById($levelJSONModel->getId());
        $rubricData->moveLevel($level, $newSort);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'level' => LevelJSONModel::fromLevel($level)
        ];
    }

    /**
     * @param string $levelJSONData
     *
     * @return LevelJSONModel
     */
    protected function parseLevelJSONModel(string $levelJSONData)
    {
        //var_dump($levelJSONData);
        $levelJSONModel = $this->serializer->deserialize(
            $levelJSONData, LevelJSONModel::class, 'json'
        );
        if (!$levelJSONModel instanceof LevelJSONModel)
        {
            throw new \RuntimeException('Could not parse the level JSON model');
        }

        return $levelJSONModel;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $choiceJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     */
    public function updateChoice(int $rubricDataId, int $versionId, string $choiceJSONData)
    {
        $choiceJSONModel = $this->parseChoiceJSONModel($choiceJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $choice = $rubricData->getChoiceByLevelAndCriteriumId(
            $choiceJSONModel->getLevelId(), $choiceJSONModel->getCriteriumId()
        );

        $choiceJSONModel->updateChoice($choice);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'level' => ChoiceJSONModel::fromChoice($choice)
        ];
    }

    /**
     * @param string $choiceJSONData
     *
     * @return ChoiceJSONModel
     */
    protected function parseChoiceJSONModel(string $choiceJSONData)
    {
        $choiceJSONModel = $this->serializer->deserialize(
            $choiceJSONData, ChoiceJSONModel::class, 'json'
        );

        if (!$choiceJSONModel instanceof ChoiceJSONModel)
        {
            throw new \RuntimeException('Could not parse the level JSON model');
        }

        return $choiceJSONModel;
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     * @param string $rubricJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateRubricData(int $rubricDataId, int $versionId, string $rubricJSONData)
    {
        $rubricJSONModel = $this->parseRubricJSONModel($rubricJSONData);
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);

        $rubricJSONModel->updateRubricData($rubricData);

        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
            'rubricData' => $rubricData
        ];
    }

    /**
     * @param int $rubricDataId
     * @param int $versionId
     *
     * @return array[]
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    public function resetRubricAbsoluteWeights(int $rubricDataId, int $versionId)
    {
        $rubricData = $this->rubricService->getRubric($rubricDataId, $versionId);
        $criteria = $rubricData->getCriteriumNodes();
        foreach ($criteria as $criterium)
        {
            if ($criterium->getWeight() != 100)
            {
                $criterium->setWeight(100);
            }
        }
        $choices = $rubricData->getChoices();
        foreach ($choices as $choice)
        {
            if ($choice->hasFixedScore())
            {
                $choice->setFixedScore(0);
                $choice->setHasFixedScore(false);
            }
        }

        $levels = $rubricData->getLevels();
        if (count($levels) > 0)
        {
            $maxScore = 0;
            foreach ($levels as $level)
            {
                $score = $level->getScore();
                if ($score > $maxScore)
                {
                    $maxScore = $score;
                }
            }
            if ($maxScore > 0)
            {
                $multiplier = 100 / $maxScore;
                foreach ($levels as $level)
                {
                    $score = $level->getScore() * $multiplier;
                    $score = round($score);
                    $level->setScore($score);
                }
            }
        }

        $rubricData->setUseRelativeWeights(true);
        $this->rubricService->saveRubric($rubricData);

        return [
            'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion(), 'levels' => $rubricData->getLevels()]
        ];
    }

    /**
     * @param string $rubricJSONData
     *
     * @return RubricJSONModel
     */
    protected function parseRubricJSONModel(string $rubricJSONData)
    {
        $rubricJSONData = $this->serializer->deserialize(
            $rubricJSONData, RubricJSONModel::class, 'json'
        );

        if (!$rubricJSONData instanceof RubricJSONModel)
        {
            throw new \RuntimeException('Could not parse the rubric JSON model');
        }

        return $rubricJSONData;
    }

}
