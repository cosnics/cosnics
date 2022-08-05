<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository as EvaluationPublicationRepository;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication as EvaluationPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationScoreService implements ScoreServiceInterface, LearningPathScoreServiceInterface
{
    /**
     * @var EvaluationPublicationRepository
     */
    protected $evaluationPublicationRepository;

    /**
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var EvaluationEntityServiceManager
     */
    protected $evaluationEntityServiceManager;

    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @param EvaluationPublicationRepository $evaluationPublicationRepository
     * @param PublicationEntityServiceManager $publicationEntityServiceManager
     * @param EvaluationEntityServiceManager $evaluationEntityServiceManager
     * @param LearningPathStepContextService $learningPathStepContextService
     */
    public function __construct(EvaluationPublicationRepository $evaluationPublicationRepository, PublicationEntityServiceManager $publicationEntityServiceManager, EvaluationEntityServiceManager $evaluationEntityServiceManager, LearningPathStepContextService $learningPathStepContextService)
    {
        $this->evaluationPublicationRepository = $evaluationPublicationRepository;
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->evaluationEntityServiceManager = $evaluationEntityServiceManager;
        $this->learningPathStepContextService = $learningPathStepContextService;
    }

    /*public function getScores(ContentObjectPublication $publication, array $userIds): array
    {
        $entityType = $this->getPublicationEntityType($publication);
        $selectedEntities = $this->getSelectedEntitiesForPublication($publication, $entityType);

        $userMap = array();
        $groups = array();
        foreach ($selectedEntities as $entity)
        {
            $members = $this->getUsersForEntity($entity['id'], $entityType);

            foreach ($members as $member)
            {
                $memberId = $member->getId();
                if (!array_key_exists($memberId, $userMap))
                {
                    $userMap[$memberId] = array();
                }
                $userMap[$memberId][] = $entity;
            }
            $groups[$entity['id']] = $entity;
        }

        $scores = array();

        foreach ($userIds as $userId)
        {
            $score = null;

            $userGroups = $userMap[$userId];
            foreach ($userGroups as $group)
            {
                if ($group['is_absent'])
                {
                    if (is_null($score))
                    {
                        $score = 'gafw';
                    }
                }
                else if (!is_null($group['score']))
                {
                    if (is_null($score))
                    {
                        $score = (float) $group['score'];
                    }
                    else if (is_numeric($score))
                    {
                        if (((float) $group['score']) > $score)
                        {
                            $score = (float) $group['score'];
                        }
                    }
                }
            }
            $scores[] = ['user_id' => (int) $userId, 'score' => $score];
        }

        return $scores;
    }*/

    /**
     * @param ContentObjectPublication $publication
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication): array
    {
        $entityType = $this->getEntityTypeFromPublication($publication);
        $contextIdentifier = new ContextIdentifier(EvaluationPublication::class_name(), $publication->getId());
        return $this->getUserScores($publication, $contextIdentifier, $entityType);
    }

    /**
     * @param ContentObjectPublication $publication
     * @param TreeNode $treeNode
     *
     * @return array
     */
    public function getScoresFromTreeNode(ContentObjectPublication $publication, TreeNode $treeNode): array
    {
        $entityType = $this->getEntityTypeFromTreeNode($treeNode);
        $contextIdentifier = $this->getContextIdentifierFromTreeNode($publication, $treeNode->getId());
        return $this->getUserScores($publication, $contextIdentifier, $entityType);
    }

    /**
     * @param ContentObjectPublication $publication
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     *
     * @return array
     */
    protected function getUserScores(ContentObjectPublication $publication, ContextIdentifier $contextIdentifier, int $entityType): array
    {
        $selectedEntities = $this->getSelectedEntitiesForPublication($publication, $contextIdentifier, $entityType);

        $scores = array();
        foreach ($selectedEntities as $entity)
        {
            $entityId = $entity['id'];
            $score = $this->getEntityScore($entity);

            if ($entityType == 0)
            {
                $scores[$entityId] = $score;
            }
            else
            {
                $users = $this->getUsersForEntity($entity['id'], $entityType);
                foreach ($users as $user)
                {
                    $userId = $user->getId();
                    $hasKey = array_key_exists($userId, $scores);
                    $curScore = $scores[$userId];
                    if (!$hasKey || (is_numeric($score) && (is_null($curScore) || $curScore == 'gafw' || (is_numeric($curScore) && $score > $curScore))))
                    {
                        $scores[$userId] = $score;
                    }
                }
            }
        }
        return $scores;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     *
     * @return RecordIterator
     */
    protected function getSelectedEntitiesForPublication(ContentObjectPublication $publication, ContextIdentifier $contextIdentifier, int $entityType): RecordIterator
    {
        $this->publicationEntityServiceManager->setContentObjectPublication($publication);
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        $entityIds = $publicationEntityService->getTargetEntityIds();
        $entityService = $this->evaluationEntityServiceManager->getEntityServiceByType($entityType);
        return $entityService->getEntitiesFromIds($entityIds, $contextIdentifier, EvaluationEntityRetrieveProperties::ALL(), new FilterParameters());
    }

    /**
     * @param int $entityId
     * @param int $entityType
     *
     * @return array
     */
    protected function getUsersForEntity(int $entityId, int $entityType): array
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->getUsersForEntity($entityId);
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return int
     */
    protected function getEntityTypeFromPublication(ContentObjectPublication $publication): int
    {
        $evaluationPublication = $this->evaluationPublicationRepository->findPublicationByContentObjectPublication($publication);
        return $evaluationPublication->getEntityType();
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return int
     */
    protected function getEntityTypeFromTreeNode(TreeNode $treeNode)
    {
        /** @var EvaluationConfiguration */
        $configuration = $treeNode->getConfiguration(new EvaluationConfiguration());
        return $configuration->getEntityType();
    }

    /**
     * @param ContentObjectPublication $publication
     * @param int $stepId
     *
     * @return ContextIdentifier
     */
    protected function getContextIdentifierFromTreeNode(ContentObjectPublication $publication, int $stepId): ContextIdentifier
    {
        $publicationClass = ContentObjectPublication::class_name();
        $publicationId = $publication->getId();
        $learningPathStepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($stepId, $publicationClass, $publicationId);
        return new ContextIdentifier(get_class($learningPathStepContext), $learningPathStepContext->getId());
    }

    /**
     * @param array $entity
     *
     * @return float|string|null
     */
    protected function getEntityScore(array $entity)
    {
        if ($entity['is_absent'])
        {
            return 'gafw';
        }

        $score = $entity['score'];
        if (!is_null($score))
        {
            return (float) $score;
        }

        return null;
    }
}