<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository as EvaluationPublicationRepository;
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
class GradeBookItemEvaluationScoreService implements GradeBookItemScoreServiceInterface
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
     * @param EvaluationPublicationRepository $evaluationPublicationRepository
     * @param PublicationEntityServiceManager $publicationEntityServiceManager
     * @param EvaluationEntityServiceManager $evaluationEntityServiceManager
     */
    public function __construct(EvaluationPublicationRepository $evaluationPublicationRepository, PublicationEntityServiceManager $publicationEntityServiceManager, EvaluationEntityServiceManager $evaluationEntityServiceManager)
    {
        $this->evaluationPublicationRepository = $evaluationPublicationRepository;
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->evaluationEntityServiceManager = $evaluationEntityServiceManager;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param array $userIds
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication, array $userIds): array
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
    }

    /**
     * @param ContentObjectPublication $publication
     * @param int $entityType
     *
     * @return RecordIterator
     */
    protected function getSelectedEntitiesForPublication(ContentObjectPublication $publication, int $entityType): RecordIterator
    {
        $this->publicationEntityServiceManager->setContentObjectPublication($publication);
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        $entityIds = $publicationEntityService->getTargetEntityIds();
        $contextIdentifier = new ContextIdentifier(EvaluationPublication::class_name(), $publication->getId());
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
     * @return int
     */
    public function getPublicationEntityType(ContentObjectPublication $publication): int
    {
        $evaluationPublication = $this->evaluationPublicationRepository->findPublicationByContentObjectPublication($publication);
        $entityType = $evaluationPublication->getEntityType();
        return $entityType;
    }
}