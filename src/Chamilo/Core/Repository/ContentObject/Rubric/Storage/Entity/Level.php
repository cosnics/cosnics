<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

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
 *
 * TODO: add sort
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
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    protected $isDefault;

    /**
     * @var RubricData
     *
     * @ORM\ManyToOne(targetEntity="RubricData")
     * @ORM\JoinColumn(name="rubric_data_id", referencedColumnName="id")
     */
    protected $rubricData;

    /**
     * Level constructor.
     *
     * @param RubricData $rubricData
     */
    public function __construct(RubricData $rubricData)
    {
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

}
