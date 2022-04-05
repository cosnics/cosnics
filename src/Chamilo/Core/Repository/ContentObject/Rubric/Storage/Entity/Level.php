<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Exclude;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @ORM\Table(
 *      name="repository_rubric_level"
 * )
 */
class Level
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer")
     */
    protected $score = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="minimum_score", type="integer", nullable=true)
     */
    protected $minimumScore = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_range_score", type="boolean")
     */
    protected $useRangeScore = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    protected $isDefault = false;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @var RubricData
     *
     * @ORM\ManyToOne(targetEntity="RubricData")
     * @ORM\JoinColumn(name="rubric_data_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $rubricData;

    /**
     * @var Choice[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="level")
     *
     * @Exclude
     */
    protected $choices;

    /**
     * @var CriteriumNode
     *
     * @ORM\ManyToOne(targetEntity="CriteriumNode")
     * @ORM\JoinColumn(name="criterium_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $criterium;

    /**
     * @var int|null
     */
    public $criterium_id = null;

    /**
     * Level constructor.
     *
     * @param RubricData $rubricData
     * @param CriteriumNode|null $criterium
     *
     */
    public function __construct(RubricData $rubricData, CriteriumNode $criterium = null)
    {
        $this->choices = new ArrayCollection();
        $this->setCriterium($criterium);
        $this->setRubricData($rubricData);
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Level
     */
    public function setId(string $id): Level
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Level
     */
    public function setTitle(string $title): Level
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Level
     */
    public function setDescription(string $description = null): Level
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    /**
     * @param int $score
     *
     * @return Level
     */
    public function setScore(int $score): Level
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinimumScore(): ?int
    {
        return $this->minimumScore;
    }

    /**
     * @param int|null $score
     *
     * @return Level
     */
    public function setMinimumScore(?int $score): Level
    {
        $this->minimumScore = $score;

        return $this;
    }

    /**
     * @return bool
     */
    public function usesRangeScore(): bool
    {
        return $this->useRangeScore;
    }

    /**
     * @param bool $useRangeScore
     *
     * @return Level
     */
    public function setUsesRangeScore(bool $useRangeScore): Level
    {
        $this->useRangeScore = $useRangeScore;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     *
     * @return Level
     */
    public function setIsDefault(bool $isDefault): Level
    {
        if($isDefault && $this->rubricData instanceof RubricData)
        {
            $this->rubricData->setCurrentDefaultLevelNoLongerDefault();
        }

        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return Level
     */
    public function setSort(int $sort): Level
    {
        if (!$this->rubricData->isLevelSortValid($this, $sort))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given level sort must be between 1 and %s, %s given',
                    count($this->rubricData->filterLevelsByCriterium($this->criterium)), $sort
                )
            );
        }

        $this->sort = $sort;

        return $this;
    }

    /**
     * @return $this
     */
    public function decrementSort()
    {
        $this->setSort($this->getSort() - 1);

        return $this;
    }

    /**
     * return $this
     */
    public function incrementSort()
    {
        $this->setSort($this->getSort() + 1);

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
     * @param RubricData|null $rubricData
     *
     * @return Level
     */
    public function setRubricData(RubricData $rubricData = null): Level
    {
        if ($this->rubricData === $rubricData)
        {
            return $this;
        }

        if (!empty($rubricData) && $this->hasCriterium())
        {
            if (!$rubricData->hasCriterium($this->criterium))
            {
                throw new \InvalidArgumentException('Criterium not part of rubric');
            }
        }

        $oldRubricData = $this->rubricData;
        $this->rubricData = $rubricData;

        if ($oldRubricData instanceof RubricData)
        {
            $oldRubricData->removeLevel($this);
        }

        if ($rubricData instanceof RubricData)
        {
            $rubricData->addLevel($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCriterium(): bool
    {
        return !(empty($this->criterium));
    }

    /**
     * @return CriteriumNode|null
     */
    public function getCriterium(): ?CriteriumNode
    {
        return $this->criterium;
    }

    /**
     * @return int|null
     */
    public function getCriteriumId(): ?int
    {
        if (isset($this->criterium))
        {
            return $this->criterium->getId();
        }

        return null;
    }

    /**
     * @param CriteriumNode|null $criterium
     *
     * @return $this
     */
    public function setCriterium(CriteriumNode $criterium = null): Level
    {
        if ($criterium === $this->criterium)
        {
            return $this;
        }

        if (isset($this->rubricData) && !empty($criterium))
        {
            if (!$this->rubricData->hasCriterium($criterium))
            {
                throw new \InvalidArgumentException('Criterium not part of rubric');
            }
        }

        if (isset($this->criterium))
        {
            $this->criterium->removeLevel($this);
        }

        $this->criterium = $criterium;
        $this->applyCriteriumId();

        if (!empty($criterium))
        {
            $criterium->addLevel($this);
        }

        return $this;
    }

    public function applyCriteriumId()
    {
        if ($this->getCriterium() instanceof CriteriumNode)
        {
            $this->criterium_id = $this->getCriterium()->getId();
            return;
        }

        $this->criterium_id = null;
    }

    /**
     * @return Choice[]|ArrayCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param Choice $choice
     *
     * @return $this
     */
    public function addChoice(Choice $choice)
    {
        if($this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->add($choice);
        $choice->setLevel($this);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return self
     */
    public function removeChoice(Choice $choice): self
    {
        if(!$this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->removeElement($choice);
        $choice->setLevel(null);

        return $this;
    }

}
