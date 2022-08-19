<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
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
 *      name="repository_gradebook_category"
 * )
 */
class GradeBookCategory
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    protected $color;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @var GradeBookColumn[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookColumn", mappedBy="gradebookCategory")
     * @ORM\OrderBy({"sort" = "asc"})
     *
     */
    protected $gradebookColumns;


    /**
     * GradeBookCategory constructor.
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
     * @return GradeBookCategory
     */
    public function setId(int $id): GradeBookCategory
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
     * @return GradeBookCategory
     */
    public function setGradeBookData(GradeBookData $gradebookData = null): GradeBookCategory
    {
        if ($this->gradebookData === $gradebookData)
        {
            return $this;
        }

        $oldGradebookData = $this->gradebookData;
        $this->gradebookData = $gradebookData;

        if ($oldGradebookData instanceof GradeBookData)
        {
            $oldGradebookData->removeGradeBookCategory($this);
        }

        if ($gradebookData instanceof GradeBookData)
        {
            $gradebookData->addGradeBookCategory($this);
        }

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
     * @return GradeBookCategory
     */
    public function setTitle(string $title): GradeBookCategory
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $title
     *
     * @return GradeBookCategory
     */
    public function setColor(string $color): GradeBookCategory
    {
        $this->color = $color;

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
     * @return GradeBookCategory
     */
    public function setSort(int $sort): GradeBookCategory
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return GradeBookCategory
     */
    public function decrementSort(): GradeBookCategory
    {
        $this->setSort($this->getSort() - 1);

        return $this;
    }

    /**
     * return GradeBookCategory
     */
    public function incrementSort(): GradeBookCategory
    {
        $this->setSort($this->getSort() + 1);

        return $this;
    }

    /**
     * @return GradeBookColumn[]|ArrayCollection
     */
    public function getGradeBookColumns()
    {
        return $this->gradebookColumns;
    }

    /**
     * This method should only be called from GradeBookColumn's setGradeBookCategory method.
     *
     * @param GradeBookColumn $gradeBookColumn
     *
     * @return GradeBookCategory
     */
    public function addGradeBookColumn(GradeBookColumn $gradeBookColumn): GradeBookCategory
    {
        if ($this->gradebookColumns->contains($gradeBookColumn))
        {
            return $this;
        }

        $this->gradebookColumns->add($gradeBookColumn);

        return $this;
    }

    /**
     * This method should only be called from GradeBookColumn's setGradeBookCategory method.
     *
     * @param GradeBookColumn $gradeBookColumnToRemove
     *
     * @return GradeBookCategory
     */
    public function removeGradeBookColumn(GradeBookColumn $gradeBookColumnToRemove): GradeBookCategory
    {
        if (!$this->gradebookColumns->contains($gradeBookColumnToRemove))
        {
            return $this;
        }

        $this->gradebookColumns->removeElement($gradeBookColumnToRemove);

        return $this;
    }

    /**
     * @return GradeBookCategoryJSONModel
     */
    public function toJSONModel(): GradeBookCategoryJSONModel
    {
        return GradeBookCategoryJSONModel::fromGradeBookCategory($this);
    }

    /**
     * @param GradeBookColumn[] $gradeBookColumnsUncategorized
     *
     * @return GradeBookCategoryJSONModel
     */
    public static function nullCategoryToJSONModel(array $gradeBookColumnsUncategorized): GradeBookCategoryJSONModel
    {
        return new GradeBookCategoryJSONModel(0, '', 'none', $gradeBookColumnsUncategorized);
    }
}
