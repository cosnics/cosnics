<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Class RubricService
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricService
{
    /**
     * @var RubricDataRepository
     */
    protected $rubricDataRepository;

    /**
     * @var RubricValidator
     */
    protected $rubricValidator;

    /**
     * @var RubricTreeBuilder
     */
    protected $rubricTreeBuilder;

    /**
     * @var RubricRightsService
     */
    protected $rubricRightsService;

    /**
     * RubricService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     * @param RubricValidator $rubricValidator
     * @param RubricTreeBuilder $rubricTreeBuilder
     * @param RubricRightsService $rubricRightsService
     */
    public function __construct(
        RubricDataRepository $rubricDataRepository, RubricValidator $rubricValidator,
        RubricTreeBuilder $rubricTreeBuilder, RubricRightsService $rubricRightsService
    )
    {
        $this->rubricDataRepository = $rubricDataRepository;
        $this->rubricValidator = $rubricValidator;
        $this->rubricTreeBuilder = $rubricTreeBuilder;
        $this->rubricRightsService = $rubricRightsService;
    }

    /**
     * Retrieves a rubric from the database
     *
     * @param int $rubricDataId
     * @param int $expectedVersion
     *
     * @return RubricData
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getRubric(int $rubricDataId, int $expectedVersion)
    {
        return $this->rubricTreeBuilder->buildRubricTreeByRubricDataId($rubricDataId, $expectedVersion);
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubric(RubricData $rubricData)
    {
        // This wont work because the object can be shared in weblcms thus using the rights from the course
        // where the object was published.
        /*if(!$this->rubricRightsService->canUserEditRubric($user, $rubricData))
        {
            throw new NotAllowedException();
        }*/

        $rubricData->setLastUpdated(new \DateTime());
        $this->rubricValidator->validateRubric($rubricData);
        $this->rubricDataRepository->saveRubricData($rubricData);
    }
}
