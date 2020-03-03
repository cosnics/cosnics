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
 * @ORM\Entity
 *
 * @ORM\Table(
 *      name="repository_rubric_result",
 *      indexes={
 *          @ORM\Index(name="rrr_context", columns={"context_class", "context_id"}),
 *          @ORM\Index(name="rrr_attempt", columns={"attempt_id"}),
 *          @ORM\Index(name="rrr_user", columns={"user_id"})
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
     * @ORM\Column(name="attempt_id", type="guid")
     */
    protected $attemptId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="attempt_time", type="datetime")
     */
    protected $attemptTime;

    /**
     * @var double
     *
     * @ORM\Column(name="score", type="decimal")
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
    public function getAttemptId(): ?string
    {
        return $this->attemptId;
    }

    /**
     * @param string $attemptId
     *
     * @return RubricResult
     */
    public function setAttemptId(string $attemptId): RubricResult
    {
        $this->attemptId = $attemptId;

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
        return $this->score;
    }

    /**
     * @param float $score
     *
     * @return RubricResult
     */
    public function setScore(float $score): RubricResult
    {
        $this->score = $score;

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
    public function setSelectedChoice(Choice $selectedChoice): RubricResult
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
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return RubricResult
     */
    public function setUserId(int $userId): RubricResult
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAttemptTime(): ?\DateTime
    {
        return $this->attemptTime;
    }

    /**
     * @param \DateTime $attemptTime
     *
     * @return RubricResult
     */
    public function setAttemptTime(\DateTime $attemptTime): RubricResult
    {
        $this->attemptTime = $attemptTime;

        return $this;
    }
}
