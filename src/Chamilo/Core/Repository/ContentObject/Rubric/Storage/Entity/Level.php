<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    protected $rubricData;

    /**
     * @var Choice[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="criterium")
     */
    protected $choices;

    /**
     * Level constructor.
     *
     * @param RubricData $rubricData
     */
    public function __construct(RubricData $rubricData)
    {
        $this->choices = new ArrayCollection();
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
     * @param string $description
     *
     * @return Level
     */
    public function setDescription(string $description): Level
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
        if (!$this->rubricData->isLevelSortValid($sort))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given level sort must be between 1 and %s, %s given',
                    $this->rubricData->getLevels()->count(), $sort
                )
            );
        }

        $this->sort = $sort;

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
     * @return Level
     */
    public function setRubricData(RubricData $rubricData = null): Level
    {
        if($this->rubricData === $rubricData)
        {
            return $this;
        }

        $oldRubricData = $this->rubricData;
        $this->rubricData = $rubricData;

        if($oldRubricData instanceof RubricData)
        {
            $oldRubricData->removeLevel($this);
        }

        if($rubricData instanceof RubricData)
        {
            $rubricData->addLevel($this);
        }

        return $this;
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
