<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\RubricResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\RubricUserJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\User\Service\UserService;
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
     */
    public function generateRubricResultsJSON(RubricData $rubricData, ContextIdentifier $contextIdentifier)
    {
        $jsonResults = [];

        $rubricResults = $this->rubricResultService->getRubricResultsForContext($rubricData, $contextIdentifier);
        foreach ($rubricResults as $rubricResult)
        {
            if (!array_key_exists($rubricResult->getResultId(), $jsonResults))
            {
                $user = $this->userService->findUserByIdentifier($rubricResult->getEvaluatorUserId());
                $targetUser = $this->userService->findUserByIdentifier($rubricResult->getTargetUserId());

                $jsonResults[$rubricResult->getResultId()] = new RubricResultJSONModel(
                    new RubricUserJSONModel($user->getId(), $user->get_fullname()),
                    new RubricUserJSONModel($targetUser->getId(), $targetUser->get_fullname()),
                    $rubricResult->getTime()
                );
            }

            $jsonResult = $jsonResults[$rubricResult->getResultId()];

            $levelId = $rubricResult->getSelectedChoice() instanceof Choice ? $rubricResult->getSelectedChoice()->getLevel()->getId() : null;

            $treeNodeResultJsonModel = new TreeNodeResultJSONModel(
                $rubricResult->getTreeNode()->getId(),
                $rubricResult->getScore(), $levelId, $rubricResult->getComment()
            );

            $jsonResult->addTreeNodeResult($treeNodeResultJsonModel);
        }

        return array_values($jsonResults);
    }

}
