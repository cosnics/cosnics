<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use function sprintf;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @deprecated The rights should be determined by the context in which the rubric builder is run. The repository alone
 * can not lock down the rights for the builder since it differs from where it has been published. Therefor the context
 * which runs the rubric service always needs to protect the rubric builder with the correct right checks.
 */
class RubricRightsService
{
    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var RightsService
     */
    protected $workspaceRightsService;

    /**
     * RubricRightsService constructor.
     *
     * @param ContentObjectRepository $contentObjectRepository
     * @param RightsService $workspaceRightsService
     */
    public function __construct(ContentObjectRepository $contentObjectRepository, RightsService $workspaceRightsService)
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->workspaceRightsService = $workspaceRightsService;
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     *
     * @return bool
     */
    public function canUserEditRubric(User $user, RubricData $rubricData)
    {
        $rubricId = $rubricData->getContentObjectId();
        $contentObject = $this->contentObjectRepository->findById($rubricId);
        if (!$contentObject instanceof Rubric)
        {
            throw new \RuntimeException(
                sprintf('Rubric content object for rubric data %s not found', $rubricData->getId())
            );
        }

        return $this->workspaceRightsService->canEditContentObject($user, $contentObject);
    }
}
