<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResultTargetUser;

/**
 * Class MigrationService
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 *
 */
class MigrationService
{
    /**
     * @var RubricResultRepository
     */
    protected $rubricResultRepository;

    /**
     * MigrationService constructor.
     *
     * @param RubricResultRepository $rubricResultRepository
     */
    public function __construct(RubricResultRepository $rubricResultRepository)
    {
        $this->rubricResultRepository = $rubricResultRepository;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function migrateTargetUsers()
    {
        $results = $this->rubricResultRepository->findBy([], ['time' => 'ASC']);
        $groupedArray = [];

        foreach ($results as $result)
        {
            $timestamp = $result->getTime()->getTimestamp();
            $resultId = $result->getResultId();
            $hash = null;

            for ($i = 0; $i <= 5; $i++)
            {
                $tempHash = $this->createHash($result, $timestamp - $i);
                if (array_key_exists($tempHash, $groupedArray))
                {
                    $hash = $tempHash;
                    break;
                }
            }

            if ($hash == null)
            {
                $hash = $this->createHash($result, $timestamp);
                $groupedArray[$hash] = [];
            }

            if (!array_key_exists($resultId, $groupedArray[$hash]))
            {
                $groupedArray[$hash][$resultId] = [];
            }

            $groupedArray[$hash][$resultId][] = $result;
        }

        foreach ($groupedArray as $hashedArray)
        {
            $keptResultId = null;

            foreach ($hashedArray as $resultId => $results)
            {
                $targetId = $results[0]->getTargetUserId();

                if ($keptResultId == null)
                {
                    $keptResultId = $resultId;
                }

                // Write target_user_id together with the first result_id (from first set of results) to RubricResultTargetUser
                $rubricResultTargetUser = new RubricResultTargetUser();
                $rubricResultTargetUser->setRubricResultGUID($keptResultId);
                $rubricResultTargetUser->setTargetUserId($targetId);

                $this->rubricResultRepository->saveRubricResultTargetUser($rubricResultTargetUser, false);

                if ($keptResultId != $resultId)
                {
                    // Remove 'duplicate' entries from subsequent sets of results
                    foreach ($results as $result)
                    {
                        $this->rubricResultRepository->removeRubricResult($result, false);
                    }
                }
            }
        }
        $this->rubricResultRepository->flush();
        // If everything went well, the target_user_id column can now be removed from RubricResult table.
    }

    /**
     * @param RubricResult $result
     * @param int $timestamp
     * @return string
     */
    protected function createHash(RubricResult $result, int $timestamp)
    {
        return md5($result->getContextClass() . ':' . $result->getContextId() . ':' . $result->getEvaluatorUserId() . ':' . $timestamp);
    }
}