<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\Assignment\Storage\Repository\AssignmentRubricRepository")
 *
 * @ORM\Table(
 *      name="repository_assignment_rubric"
 * )
 */
class AssignmentRubric
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="assignment_id", type="integer")
     */
    protected $assignmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="rubric_id", type="integer")
     */
    protected $rubricId;

    /**
     * @var bool
     *
     * @ORM\Column(name="self_evaluation_allowed", type="boolean")
     */
    protected $selfEvaluationAllowed = false;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AssignmentRubric
     */
    public function setId(int $id): AssignmentRubric
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getAssignmentId(): ?int
    {
        return $this->assignmentId;
    }

    /**
     * @param int $assignmentId
     *
     * @return AssignmentRubric
     */
    public function setAssignmentId(int $assignmentId): AssignmentRubric
    {
        $this->assignmentId = $assignmentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getRubricId(): ?int
    {
        return $this->rubricId;
    }

    /**
     * @param int $rubricId
     *
     * @return AssignmentRubric
     */
    public function setRubricId(int $rubricId): AssignmentRubric
    {
        $this->rubricId = $rubricId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSelfEvaluationAllowed(): ?bool
    {
        return $this->selfEvaluationAllowed;
    }

    /**
     * @param bool $selfEvaluationAllowed
     *
     * @return AssignmentRubric
     */
    public function setSelfEvaluationAllowed(bool $selfEvaluationAllowed): AssignmentRubric
    {
        $this->selfEvaluationAllowed = $selfEvaluationAllowed;

        return $this;
    }

}
