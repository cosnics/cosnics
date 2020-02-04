<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
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
     * RubricService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     * @param RubricValidator $rubricValidator
     * @param RubricTreeBuilder $rubricTreeBuilder
     */
    public function __construct(
        RubricDataRepository $rubricDataRepository, RubricValidator $rubricValidator,
        RubricTreeBuilder $rubricTreeBuilder
    )
    {
        $this->rubricDataRepository = $rubricDataRepository;
        $this->rubricValidator = $rubricValidator;
        $this->rubricTreeBuilder = $rubricTreeBuilder;
    }

    /**
     * Retrieves a rubric from the database
     *
     * @param int $rubricDataId
     *
     * @return RubricData
     */
    public function getRubric(int $rubricDataId)
    {
        $rubric = $this->rubricTreeBuilder->buildRubricTreeByRubricDataId($rubricDataId);

        if(!$rubric instanceof RubricData)
        {
            throw new \RuntimeException('Rubric with id %s not found');
        }

        return $rubric;
    }

    /**
     * @param string $rubricName
     * @param bool $useScores
     *
     * @return RubricData
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     */
    public function createRubric(string $rubricName, bool $useScores = true)
    {
        $rubricData = new RubricData($rubricName, $useScores);

        $this->saveRubric($rubricData);

        return $rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     */
    public function saveRubric(RubricData $rubricData)
    {
        $this->rubricValidator->validateRubric($rubricData);
        $this->rubricDataRepository->saveRubricData($rubricData);
    }
}
