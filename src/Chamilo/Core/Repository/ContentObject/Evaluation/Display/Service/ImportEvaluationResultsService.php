<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportEvaluationResultsService
{
    /**
     * @var EvaluationEntryService
     */
    protected $evaluationEntryService;

    public function __construct(EvaluationEntryService $evaluationEntryService)
    {
        $this->evaluationEntryService = $evaluationEntryService;
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param ContextIdentifier $contextId
     * @param array $results
     * @return array
     */
    public function importResults(int $evaluationId, int $evaluatorId, ContextIdentifier $contextId, array $results): array
    {
        $importedEntities = array();
        $evaluationEntryScores = array();

        foreach ($results as $result)
        {
            $entityId = $result['id'];
            $score = $result['score'];
            $evaluationEntryScores[] = $this->evaluationEntryService->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $evaluatorId, $contextId, 0, $entityId, $score);
            $importedEntities[] = $entityId;
        }

        return ['evaluationEntryScores' => $evaluationEntryScores, 'importedEntities' => $importedEntities];
    }

    /**
     * @param array $users
     * @param array $importedEntities
     * @return array
     */
    public function findMissingUsers(array $users, array $importedEntities): array
    {
        $missingUsers = $users;
        foreach ($importedEntities as $entityId)
        {
            $missingUsers = $this->purgeUser($missingUsers, $entityId);
        }
        return $missingUsers;
    }

    /**
     * @param array $users
     * @param string $userId
     * @return array
     */
    private function purgeUser(array $users, string $userId): array
    {
        $f = function($user) use ($userId)
        {
            return $user['id'] !== $userId;
        };

        return array_filter($users, $f);
    }

    /**
     * @param array $users
     * @return array
     */
    public function filterUserFields(array $users): array
    {
        return array_values(array_map([$this, 'filterUserField'], $users));
    }

    private function filterUserField(array $user)
    {
        return ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'official_code' => $user['official_code']];
    }
}