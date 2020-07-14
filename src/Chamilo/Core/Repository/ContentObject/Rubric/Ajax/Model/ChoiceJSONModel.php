<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChoiceJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $selected;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $feedback;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $hasFixedScore;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $fixedScore;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $levelId;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $criteriumId;

    /**
     * ChoiceJSONModel constructor.
     *
     * @param int $id
     * @param int $levelId
     * @param int $criteriumId
     * @param bool $selected
     * @param string $feedback
     * @param bool $hasFixedScore
     * @param int $fixedScore
     */
    public function __construct(
        int $id, int $levelId, int $criteriumId, bool $selected, string $feedback, bool $hasFixedScore, int $fixedScore
    )
    {
        $this->id = $id;
        $this->selected = $selected;
        $this->feedback = $feedback;
        $this->hasFixedScore = $hasFixedScore;
        $this->fixedScore = $fixedScore;
        $this->levelId = $levelId;
        $this->criteriumId = $criteriumId;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isSelected(): ?bool
    {
        return $this->selected;
    }

    /**
     * @return string
     */
    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    /**
     * @return bool
     */
    public function isHasFixedScore(): ?bool
    {
        return $this->hasFixedScore;
    }

    /**
     * @return int
     */
    public function getFixedScore(): ?int
    {
        return $this->fixedScore;
    }

    /**
     * @return int
     */
    public function getLevelId(): ?int
    {
        return $this->levelId;
    }

    /**
     * @param int $levelId
     *
     * @return ChoiceJSONModel
     */
    public function setLevelId(int $levelId): ChoiceJSONModel
    {
        $this->levelId = $levelId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCriteriumId(): ?int
    {
        return $this->criteriumId;
    }

    /**
     * @param int $criteriumId
     *
     * @return ChoiceJSONModel
     */
    public function setCriteriumId(int $criteriumId): ChoiceJSONModel
    {
        $this->criteriumId = $criteriumId;

        return $this;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return Choice
     */
    public function toChoice(RubricData $rubricData)
    {
        $choice = new Choice($rubricData);

        $this->updateChoice($choice);

        return $choice;
    }

    /**
     * @param Choice $choice
     *
     * @return Choice
     */
    public function updateChoice(Choice $choice)
    {
        //$choice->setId($this->id);
        $choice->setSelected($this->selected);
        $choice->setFeedback($this->feedback);
        $choice->setHasFixedScore($this->hasFixedScore);
        $choice->setFixedScore($this->fixedScore);

        return $choice;
    }

    /**
     * @param Choice $choice
     *
     * @return ChoiceJSONModel
     */
    public static function fromChoice(Choice $choice)
    {
        $levelId = $choice->getLevel() instanceof Level ? $choice->getLevel()->getId() : 0;
        $criteriumId = $choice->getCriterium() instanceof CriteriumNode ? $choice->getCriterium()->getId() : 0;

        return new self(
            $choice->getId(), $levelId, $criteriumId, $choice->isSelected(), $choice->getFeedback(),
            $choice->hasFixedScore(), $choice->getFixedScore()
        );
    }
}
