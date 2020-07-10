<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\Entity\AssignmentRubric;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;

/**
 * Class RubricResultRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRubricRepository extends CommonEntityRepository
{

    /**
     * @param AssignmentRubric $assignmentRubric
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveAssignmentRubric(AssignmentRubric $assignmentRubric)
    {
        $this->saveEntity($assignmentRubric);
    }

    /**
     * @param Assignment $assignment
     *
     * @return AssignmentRubric|object
     */
    public function getAssignmentRubricForAssignment(Assignment $assignment)
    {
        return $this->findOneBy(['assignmentId' => $assignment->getId()]);
    }
}
