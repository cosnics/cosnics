<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;

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
     * @var RubricResultService
     */
    protected $rubricResultService;

    /**
     * RubricService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     * @param RubricValidator $rubricValidator
     * @param RubricTreeBuilder $rubricTreeBuilder
     * @param RubricResultService $rubricResultService
     */
    public function __construct(
        RubricDataRepository $rubricDataRepository, RubricValidator $rubricValidator,
        RubricTreeBuilder $rubricTreeBuilder, RubricResultService $rubricResultService
    )
    {
        $this->rubricDataRepository = $rubricDataRepository;
        $this->rubricValidator = $rubricValidator;
        $this->rubricTreeBuilder = $rubricTreeBuilder;
        $this->rubricResultService = $rubricResultService;
    }

    /**
     * Retrieves a rubric from the database
     *
     * @param int $rubricDataId
     * @param int $expectedVersion
     *
     * @return RubricData
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function getRubric(int $rubricDataId, int $expectedVersion = null)
    {
        return $this->rubricTreeBuilder->buildRubricTreeByRubricDataId($rubricDataId, $expectedVersion);
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     * @throws RubricHasResultsException
     */
    public function saveRubric(RubricData $rubricData)
    {
        if(!$this->canChangeRubric($rubricData))
        {
            throw new RubricHasResultsException();
        }

        $rubricData->setLastUpdated(new \DateTime());
        $this->rubricValidator->validateRubric($rubricData);

        $this->rubricDataRepository->saveRubricData($rubricData);
    }

    /**
     * This method checks if the rubric can be changed. This is a global check and does not check for specific rights
     * for a given user.
     *
     * @param RubricData $rubricData
     *
     * @return bool
     */
    public function canChangeRubric(RubricData $rubricData)
    {
        return !$this->rubricResultService->doesRubricHaveResults($rubricData);
    }

    /**
     * @param int $rubricDataId
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteRubricData(int $rubricDataId)
    {
        $rubricData = $this->getRubric($rubricDataId);
        $this->rubricDataRepository->deleteRubricData($rubricData);
    }
}
