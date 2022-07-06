<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Exclude;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @ORM\Table(
 *      name="repository_gradebook_column"
 * )
 */
class GradeBookColumn
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
     * @var GradeBookData
     *
     * @ORM\ManyToOne(targetEntity="GradeBookData")
     * @ORM\JoinColumn(name="gradebook_data_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $gradebookData;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    protected $weight = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="count_for_end_result", type="boolean")
     */
    protected $countForEndResult = false;

    /**
     * @var int
     *
     * @ORM\Column(name="auth_presence_end_result", type="integer")
     */
    protected $authPresenceEndResult = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="unauth_presence_end_result", type="integer")
     */
    protected $unauthPresenceEndResult = 2;

    /**
     * @var GradeBookCategory
     *
     * @ORM\ManyToOne(targetEntity="GradeBookCategory")
     * @ORM\JoinColumn(name="gradebook_category_id", referencedColumnName="id", nullable=true)
     *
     * @Exclude
     */
    protected $gradebookCategory;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @var GradeBookItem[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookItem", mappedBy="gradebookColumn")
     */
    protected $gradebookColumnSubItems;

    /**
     * GradeBookColumn constructor.
     *
     * @param GradeBookData $gradebookData
     *
     */
    public function __construct(GradeBookData $gradebookData)
    {
        $this->setGradeBookData($gradebookData);
    }

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
     * @return GradeBookColumn
     */
    public function setId(int $id): GradeBookColumn
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return GradeBookData
     */
    public function getGradeBookData(): ?GradeBookData
    {
        return $this->gradebookData;
    }

    /**
     * @param GradeBookData|null $gradebookData
     *
     * @return GradeBookColumn
     */
    public function setGradeBookData(GradeBookData $gradebookData = null): GradeBookColumn
    {
        if ($this->gradebookData === $gradebookData)
        {
            return $this;
        }

        $oldGradebookData = $this->gradebookData;
        $this->gradebookData = $gradebookData;

        if ($oldGradebookData instanceof GradeBookData)
        {
            $oldGradebookData->removeGradeBookColumn($this);
        }

        if ($gradebookData instanceof GradeBookData)
        {
            $gradebookData->addGradeBookColumn($this);
        }

        return $this;
    }

    /**
     * @return GradeBookCategory|null
     */
    public function getGradeBookCategory(): ?GradeBookCategory
    {
        return $this->gradebookCategory;
    }

    /**
     * @param GradeBookCategory|null $gradebookCategory
     *
     * @return GradeBookColumn
     */
    public function setGradeBookCategory(GradeBookCategory $gradebookCategory = null): GradeBookColumn
    {
        if ($this->gradebookCategory === $gradebookCategory)
        {
            return $this;
        }

        $oldGradebookCategory = $this->gradebookCategory;
        $this->gradebookCategory = $gradebookCategory;

        if ($oldGradebookCategory instanceof GradeBookCategory)
        {
            $oldGradebookCategory->removeGradeBookColumn($this);
        }

        if ($gradebookCategory instanceof GradeBookCategory)
        {
            $gradebookCategory->addGradeBookColumn($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return GradeBookColumn
     */
    public function setType(string $type): GradeBookColumn
    {
        $this->type = $type;

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
     * @param ?string $title
     *
     * @return GradeBookColumn
     */
    public function setTitle(?string $title): GradeBookColumn
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param ?int $weight
     *
     * @return GradeBookColumn
     */
    public function setWeight(?int $weight): GradeBookColumn
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCountForEndResult(): ?bool
    {
        return $this->countForEndResult;
    }

    /**
     * @param bool $countForEndResult
     *
     * @return GradeBookColumn
     */
    public function setCountForEndResult(bool $countForEndResult): GradeBookColumn
    {
        $this->countForEndResult = $countForEndResult;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthPresenceEndResult(): ?int
    {
        return $this->authPresenceEndResult;
    }

    /**
     * @param int $authPresenceEndResult
     *
     * @return GradeBookColumn
     */
    public function setAuthPresenceEndResult(int $authPresenceEndResult): GradeBookColumn
    {
        $this->authPresenceEndResult = $authPresenceEndResult;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnauthPresenceEndResult(): ?int
    {
        return $this->unauthPresenceEndResult;
    }

    /**
     * @param int $unauthPresenceEndResult
     *
     * @return GradeBookColumn
     */
    public function setUnauthPresenceEndResult(int $unauthPresenceEndResult): GradeBookColumn
    {
        $this->unauthPresenceEndResult = $unauthPresenceEndResult;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /** @param int $sort
     *
     * @return GradeBookColumn
     */
    public function setSort(int $sort): GradeBookColumn
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return GradeBookColumn
     */
    public function decrementSort(): GradeBookColumn
    {
        $this->setSort($this->getSort() - 1);

        return $this;
    }

    /**
     * return GradeBookColumn
     */
    public function incrementSort(): GradeBookColumn
    {
        $this->setSort($this->getSort() + 1);

        return $this;
    }

    /**
     * @return GradeBookItem[]|ArrayCollection
     */
    public function getGradeBookColumnSubItems()
    {
        return $this->gradebookColumnSubItems;
    }

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return GradeBookColumn
     */
    public function addGradeBookColumnSubItem(GradeBookItem $gradeBookItem): GradeBookColumn
    {
        if ($this->gradebookColumnSubItems->contains($gradeBookItem))
        {
            return $this;
        }
        $this->gradebookColumnSubItems->add($gradeBookItem);
        $gradeBookItem->setGradeBookColumn($this);
        return $this;
    }

    /**
     * @param GradeBookItem $gradeBookItemToRemove
     *
     * @return GradeBookColumn
     */
    public function removeGradeBookColumnSubItem(GradeBookItem $gradeBookItemToRemove): GradeBookColumn
    {
        if (!$this->gradebookColumnSubItems->contains($gradeBookItemToRemove))
        {
            return $this;
        }
        $this->gradebookColumnSubItems->removeElement($gradeBookItemToRemove);
        $gradeBookItemToRemove->setGradeBookColumn(null);
        return $this;
    }
}