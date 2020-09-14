<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\Entity\AssignmentRubric;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\Repository\AssignmentRubricRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;

/**
 * Class AssignmentRubricService
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRubricService
{
    /**
     * @var ContentObjectService
     */
    protected $contentObjectService;

    /**
     * @var AssignmentRubricRepository
     */
    protected $assignmentRubricRepository;

    /**
     * AssignmentRubricService constructor.
     *
     * @param ContentObjectService $contentObjectService
     * @param AssignmentRubricRepository $assignmentRubricRepository
     */
    public function __construct(
        ContentObjectService $contentObjectService, AssignmentRubricRepository $assignmentRubricRepository
    )
    {
        $this->contentObjectService = $contentObjectService;
        $this->assignmentRubricRepository = $assignmentRubricRepository;
    }

    /**
     * @param Assignment $assignment
     * @param int $rubricId
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function attachRubricToAssignmentById(Assignment $assignment, int $rubricId)
    {
        $rubric = $this->contentObjectService->findById($rubricId);
        if(!$rubric instanceof Rubric)
        {
            throw new \InvalidArgumentException('There is no rubric content object available with id ' . $rubricId);
        }

        $assigmentRubric = new AssignmentRubric();
        $assigmentRubric->setAssignmentId($assignment->getId());
        $assigmentRubric->setRubricId($rubricId);

        $assignment->include_content_object($rubricId);

        $this->assignmentRubricRepository->saveAssignmentRubric($assigmentRubric);
    }

    /**
     * @param Assignment $assignment
     *
     * @return Rubric|\Chamilo\Core\Repository\Storage\DataClass\ContentObject|null
     */
    public function getRubricForAssignment(Assignment $assignment)
    {
        $assignmentRubric = $this->assignmentRubricRepository->getAssignmentRubricForAssignment($assignment);
        if(!$assignmentRubric instanceof AssignmentRubric)
        {
            return null;
        }

        $rubric = $this->contentObjectService->findById($assignmentRubric->getRubricId());
        if(!$rubric instanceof Rubric)
        {
            return null;
        }

        return $rubric;
    }

    /**
     * @param Assignment $assignment
     *
     * @return bool
     */
    public function assignmentHasRubric(Assignment $assignment)
    {
        $assignmentRubric = $this->assignmentRubricRepository->getAssignmentRubricForAssignment($assignment);
        return $assignmentRubric instanceof AssignmentRubric;
    }

    /**
     * @param Assignment $assignment
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function toggleRubricSelfEvaluationComponent(Assignment $assignment)
    {
        $assignmentRubric = $this->assignmentRubricRepository->getAssignmentRubricForAssignment($assignment);
        if(!$assignmentRubric instanceof AssignmentRubric)
        {
            return;
        }

        $assignmentRubric->setSelfEvaluationAllowed(!$assignmentRubric->isSelfEvaluationAllowed());

        $this->assignmentRubricRepository->saveAssignmentRubric($assignmentRubric);
    }

    /**
     * @param Assignment $assignment
     *
     * @return bool|null
     */
    public function isSelfEvaluationAllowed(Assignment $assignment)
    {
        $assignmentRubric = $this->assignmentRubricRepository->getAssignmentRubricForAssignment($assignment);
        if(!$assignmentRubric instanceof AssignmentRubric)
        {
            return false;
        }

        return $assignmentRubric->isSelfEvaluationAllowed();
    }

}
