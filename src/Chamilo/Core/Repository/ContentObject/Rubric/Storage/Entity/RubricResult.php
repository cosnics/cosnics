<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RubricResult
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository")
 *
 * @ORM\Table(
 *      name="repository_rubric_result",
 *      indexes={
 *          @ORM\Index(name="rrr_context", columns={"context_class", "context_id"}),
 *          @ORM\Index(name="rrr_attempt", columns={"result_id"}),
 *          @ORM\Index(name="rrr_target_user", columns={"target_user_id"})
 *      }
 * )
 */
class RubricResult
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
     * @var string
     *
     * @ORM\Column(name="context_class", type="string")
     */
    protected $contextClass;

    /**
     * @var int
     *
     * @ORM\Column(name="context_id", type="integer")
     */
    protected $contextId;

    /**
     * GUID for unique ID
     *
     * @var string
     *
     * @ORM\Column(name="result_id", type="guid")
     */
    protected $resultId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $evaluatorUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="target_user_id", type="integer")
     */
    protected $targetUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    protected $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    protected $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @var RubricData
     *
     * @ORM\ManyToOne(targetEntity="RubricData")
     * @ORM\JoinColumn(name="rubric_data_id", referencedColumnName="id")
     */
    protected $rubricData;

    /**
     * @var TreeNode
     *
     * @ORM\ManyToOne(targetEntity="TreeNode")
     * @ORM\JoinColumn(name="tree_node_id", referencedColumnName="id")
     */
    protected $treeNode;

    /**
     * @var Choice
     *
     * @ORM\ManyToOne(targetEntity="Choice")
     * @ORM\JoinColumn(name="choice_id", referencedColumnName="id")
     */
    protected $selectedChoice;

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
     * @return RubricResult
     */
    public function setId(int $id): RubricResult
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getResultId(): ?string
    {
        return $this->resultId;
    }

    /**
     * @param string $resultId
     *
     * @return RubricResult
     */
    public function setResultId(string $resultId): RubricResult
    {
        $this->resultId = $resultId;

        return $this;
    }

    /**
     * @return RubricData
     */
    public function getRubricData(): ?RubricData
    {
        return $this->rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return RubricResult
     */
    public function setRubricData(RubricData $rubricData): RubricResult
    {
        $this->rubricData = $rubricData;

        return $this;
    }

    /**
     * @return TreeNode
     */
    public function getTreeNode(): ?TreeNode
    {
        return $this->treeNode;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return RubricResult
     */
    public function setTreeNode(TreeNode $treeNode): RubricResult
    {
        $this->treeNode = $treeNode;

        return $this;
    }

    /**
     * @return float
     */
    public function getScore(): ?float
    {
        return $this->score / 100;
    }

    /**
     * @param float $score
     *
     * @return RubricResult
     */
    public function setScore(float $score): RubricResult
    {
        $this->score = (int) (round($score, 2) * 100);

        return $this;
    }

    /**
     * @return Choice
     */
    public function getSelectedChoice(): ?Choice
    {
        return $this->selectedChoice;
    }

    /**
     * @param Choice $selectedChoice
     *
     * @return RubricResult
     */
    public function setSelectedChoice(Choice $selectedChoice = null): RubricResult
    {
        $this->selectedChoice = $selectedChoice;

        return $this;
    }

    /**
     * @return string
     */
    public function getContextClass(): ?string
    {
        return $this->contextClass;
    }

    /**
     * @param string $contextClass
     *
     * @return RubricResult
     */
    public function setContextClass(string $contextClass): RubricResult
    {
        $this->contextClass = $contextClass;

        return $this;
    }

    /**
     * @return int
     */
    public function getContextId(): ?int
    {
        return $this->contextId;
    }

    /**
     * @param int $contextId
     *
     * @return RubricResult
     */
    public function setContextId(int $contextId): RubricResult
    {
        $this->contextId = $contextId;

        return $this;
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     *
     * @return $this
     */
    public function setContextIdentifier(ContextIdentifier $contextIdentifier)
    {
        $this->setContextClass($contextIdentifier->getContextClass());
        $this->setContextId($contextIdentifier->getContextId());

        return $this;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return new ContextIdentifier($this->getContextClass(), $this->getContextId());
    }

    /**
     * @return int
     */
    public function getEvaluatorUserId(): ?int
    {
        return $this->evaluatorUserId;
    }

    /**
     * @param int $evaluatorUserId
     *
     * @return RubricResult
     */
    public function setEvaluatorUserId(int $evaluatorUserId): RubricResult
    {
        $this->evaluatorUserId = $evaluatorUserId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTargetUserId(): ?int
    {
        return $this->targetUserId;
    }

    /**
     * @param int $targetUserId
     *
     * @return RubricResult
     */
    public function setTargetUserId(int $targetUserId): RubricResult
    {
        $this->targetUserId = $targetUserId;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return RubricResult
     */
    public function setComment(string $comment = null): RubricResult
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     *
     * @return RubricResult
     */
    public function setTime(\DateTime $time): RubricResult
    {
        $this->time = $time;

        return $this;
    }
}
