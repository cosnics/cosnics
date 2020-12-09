<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\RubricResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\RubricUserJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class RubricResultJSONGenerator
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 */
class RubricResultJSONGenerator
{
    /**
     * @var RubricResultService
     */
    protected $rubricResultService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * RubricResultJSONGenerator constructor.
     *
     * @param RubricResultService $rubricResultService
     * @param UserService $userService
     */
    public function __construct(RubricResultService $rubricResultService, UserService $userService)
    {
        $this->rubricResultService = $rubricResultService;
        $this->userService = $userService;
    }

    /**
     * Generates all the rubric results as
     *
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param User|null $targetUser
     *
     * @return array
     */
    public function generateRubricResultsJSON(
        RubricData $rubricData, ContextIdentifier $contextIdentifier, User $targetUser = null
    )
    {
        $handledAttempts = [];
        $jsonResults = [];

        $rubricResults = $this->rubricResultService->getRubricResultsForContext(
            $rubricData, $contextIdentifier, $targetUser
        );

        foreach ($rubricResults as $rubricResult)
        {
            $uniqueAttemptId = md5($rubricResult->getEvaluatorUserId() . ':' . $rubricResult->getTime()->getTimestamp());
            if (!array_key_exists($rubricResult->getResultId(), $jsonResults))
            {
                // Prevent to handle the same attempt twice: e.g. when a user evaluates a group then two individual
                // results are generated but they must be shown as one group result. Since a user can only evaluate
                // once at the same moment, the results are ignored after the first user has been processed
                if(array_key_exists($uniqueAttemptId, $handledAttempts))
                {
                    continue;
                }

                $user = $this->userService->findUserByIdentifier($rubricResult->getEvaluatorUserId());
                $targetUser = $this->userService->findUserByIdentifier($rubricResult->getTargetUserId());

                $jsonResults[$rubricResult->getResultId()] = new RubricResultJSONModel(
                    new RubricUserJSONModel($user->getId(), $user->get_fullname()),
                    new RubricUserJSONModel($targetUser->getId(), $targetUser->get_fullname()),
                    $rubricResult->getTime()
                );

                $handledAttempts[$uniqueAttemptId] = true;
            }

            $jsonResult = $jsonResults[$rubricResult->getResultId()];

            $levelId = $rubricResult->getSelectedChoice() instanceof Choice ?
                $rubricResult->getSelectedChoice()->getLevel()->getId() : null;

            $treeNodeResultJsonModel = new TreeNodeResultJSONModel(
                $rubricResult->getTreeNode()->getId(),
                $rubricResult->getScore(), $levelId, $rubricResult->getComment()
            );

            $jsonResult->addTreeNodeResult($treeNodeResultJsonModel);
        }

        return array_values($jsonResults);
    }

}
