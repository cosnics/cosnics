<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookDataJSONModel;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository\GradeBookDataRepository")
 *
 * @ORM\Table(
 *      name="repository_gradebook_data"
 * )
 *
 */
class GradeBookData
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
     * @ORM\Version
     * @ORM\Column(name="version", type="integer")
     *
     */
    protected $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime")
     */
    protected $lastUpdated;

    /**
     * @var int
     *
     * @ORM\Column(name="content_object_id", type="integer", nullable=true)
     */
    protected $contentObjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var GradeBookItem[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookItem", mappedBy="gradebookData")
     */
    protected $gradebookItems;

    /**
     * @var GradeBookColumn[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookColumn", mappedBy="gradebookData")
     * @ORM\OrderBy({"sort" = "asc"})
     *
     */
    protected $gradebookColumns;

    /**
     * @var GradeBookCategory[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookCategory", mappedBy="gradebookData")
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $gradebookCategories;

    /**
     * @var GradeBookScore[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookScore", mappedBy="gradebookData")
     */
    protected $gradebookScores;

    /**
     * Keeps track of removed entities so they can be removed from the database after
     *
     * @var ArrayCollection
     */
    protected $removedEntities;

    /**
     * GradeBookData constructor.
     *
     * @param string $title
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function __construct(string $title)
    {
        $this->title = $title;
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
     * @return GradeBookData
     */
    public function setId(int $id): GradeBookData
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return GradeBookData
     */
    public function setVersion(int $version): GradeBookData
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }

    /**
     * @param \DateTime $lastUpdated
     *
     * @return GradeBookData
     */
    public function setLastUpdated(\DateTime $lastUpdated): GradeBookData
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * @return int
     */
    public function getContentObjectId(): ?int
    {
        return $this->contentObjectId;
    }

    /**
     * @param int $contentObjectId
     *
     * @return GradeBookData
     */
    public function setContentObjectId(int $contentObjectId): GradeBookData
    {
        $this->contentObjectId = $contentObjectId;

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
     * @return GradeBookData
     */
    public function setTitle(string $title): GradeBookData
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return GradeBookItem[]|ArrayCollection
     */
    public function getGradeBookItems()
    {
        return $this->gradebookItems;
    }

    /**
     * @param int $itemId
     *
     * @return GradeBookItem
     *
     * @throws ObjectNotExistException
     */
    public function getGradeBookItemById(int $itemId): GradeBookItem
    {
        $item = $this->gradebookItems->filter(function(GradeBookItem $item) use ($itemId) {
            return $item->getId() == $itemId;
        })->first();

        if (!$item instanceof GradeBookItem)
        {
            throw new ObjectNotExistException('gradebook item', $itemId);
        }

        return $item;
    }

    /**
     * @param GradeBookItem $gradebookItem
     *
     * @return GradeBookData
     */
    public function addGradeBookItem(GradeBookItem $gradebookItem): GradeBookData
    {
        if ($this->gradebookItems->contains($gradebookItem))
        {
            return $this;
        }

        $this->gradebookItems->add($gradebookItem);
        $gradebookItem->setGradeBookData($this);

        return $this;
    }

    /**
     * @param GradeBookItem $gradeBookItemToRemove
     *
     * @return GradeBookData
     */
    public function removeGradeBookItem(GradeBookItem $gradeBookItemToRemove): GradeBookData
    {
        if (!$this->gradebookItems->contains($gradeBookItemToRemove))
        {
            return $this;
        }

        $this->gradebookItems->removeElement($gradeBookItemToRemove);
        $gradeBookItemToRemove->setGradeBookData(null);

        $this->getRemovedEntities()->add($gradeBookItemToRemove);

        return $this;
    }

    /**
     * @return GradeBookCategory[]|ArrayCollection
     */
    public function getGradeBookCategories()
    {
        return $this->gradebookCategories;
    }

    /**
     * @param int $categoryId
     *
     * @return GradeBookCategory
     *
     * @throws ObjectNotExistException
     */
    public function getGradeBookCategoryById(int $categoryId)
    {
        $category = $this->gradebookCategories->filter(function(GradeBookCategory $category) use ($categoryId) {
            return $category->getId() == $categoryId;
        })->first();

        if (!$category instanceof GradeBookCategory)
        {
            throw new ObjectNotExistException('gradebook category', $categoryId);
        }

        return $category;
    }

    /**
     * @param GradeBookCategory $gradebookCategory
     *
     * @return GradeBookData
     */
    public function addGradeBookCategory(GradeBookCategory $gradebookCategory): GradeBookData
    {
        if ($this->gradebookCategories->contains($gradebookCategory))
        {
            return $this;
        }

        $this->gradebookCategories->add($gradebookCategory);
        $gradebookCategory->setGradeBookData($this);
        $gradebookCategory->setSort(count($this->gradebookCategories));

        return $this;
    }

    /**
     * @param GradeBookCategory $gradeBookCategoryToRemove
     *
     * @return GradeBookData
     */
    public function removeGradeBookCategory(GradeBookCategory $gradeBookCategoryToRemove): GradeBookData
    {
        if (!$this->gradebookCategories->contains($gradeBookCategoryToRemove))
        {
            return $this;
        }

        $this->gradebookCategories->removeElement($gradeBookCategoryToRemove);
        $gradeBookCategoryToRemove->setGradeBookData(null);

        foreach ($this->gradebookCategories as $category)
        {
            if ($category->getSort() >= $gradeBookCategoryToRemove->getSort())
            {
                $category->decrementSort();
            }
        }

        $this->getRemovedEntities()->add($gradeBookCategoryToRemove);

        return $this;
    }

    /**
     * @param GradeBookCategory $gradeBookCategory
     * @param int $newSort
     *
     * @return GradeBookData
     */
    public function moveGradeBookCategory(GradeBookCategory $gradeBookCategory, int $newSort): GradeBookData
    {
        if (!$this->gradebookCategories->contains($gradeBookCategory))
        {
            throw new \InvalidArgumentException(
                sprintf('The given category %s is not available in gradebook data %s', $gradeBookCategory->getId(), $this->getId())
            );
        }

        $oldSort = $gradeBookCategory->getSort();

        foreach ($this->gradebookCategories as $category)
        {
            if ($category == $gradeBookCategory)
            {
                continue;
            }

            if ($category->getSort() >= $oldSort)
            {
                $category->decrementSort();
            }

            if ($category->getSort() >= $newSort)
            {
                $category->incrementSort();
            }
        }

        $gradeBookCategory->setSort($newSort);

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
     * @param GradeBookCategory|null $category
     *
     * @return array|GradeBookColumn[]|ArrayCollection
     */
    public function getGradeBookCategoryColumns(?GradeBookCategory $category)
    {
        if ($category instanceof GradeBookCategory)
        {
            return $category->getGradeBookColumns();
        }

        return $this->getGradeBookColumnsUncategorized();
    }

    /**
     * @return array
     */
    public function getGradeBookColumnsUncategorized(): array
    {
        $columns = array();
        foreach ($this->gradebookColumns as $column)
        {
            if (is_null($column->getGradeBookCategory()))
            {
                $columns[] = $column;
            }
        }
        return $columns;
    }

    /**
     * @param int $columnId
     *
     * @return GradeBookColumn
     *
     * @throws ObjectNotExistException
     */
    public function getGradeBookColumnById(int $columnId)
    {
        $column = $this->gradebookColumns->filter(function(GradeBookColumn $column) use ($columnId) {
            return $column->getId() == $columnId;
        })->first();

        if (!$column instanceof GradeBookColumn)
        {
            throw new ObjectNotExistException('gradebook column', $columnId);
        }

        return $column;
    }

    /**
     * @param GradeBookColumn $gradeBookColumn
     *
     * @return GradeBookData
     */
    public function addGradeBookColumn(GradeBookColumn $gradeBookColumn): GradeBookData
    {
        if ($this->gradebookColumns->contains($gradeBookColumn))
        {
            return $this;
        }

        $this->gradebookColumns->add($gradeBookColumn);
        $gradeBookColumn->setGradeBookData($this);
        $gradeBookColumn->setSort(count($this->getGradeBookColumnsUncategorized()));

        return $this;
    }

    /**
     * @param GradeBookColumn $gradeBookColumnToRemove
     *
     * @return GradeBookData
     */
    public function removeGradeBookColumn(GradeBookColumn $gradeBookColumnToRemove): GradeBookData
    {
        if (!$this->gradebookColumns->contains($gradeBookColumnToRemove))
        {
            return $this;
        }

        $this->gradebookColumns->removeElement($gradeBookColumnToRemove);

        $category = $gradeBookColumnToRemove->getGradeBookCategory();

        $items = $gradeBookColumnToRemove->getGradeBookColumnSubItems();
        foreach ($items as $item)
        {
            $gradeBookColumnToRemove->removeGradeBookColumnSubItem($item);
        }

        $gradeBookColumnToRemove->setGradeBookCategory(null);
        $gradeBookColumnToRemove->setGradeBookData(null);

        foreach ($this->getGradeBookCategoryColumns($category) as $column)
        {
            if ($gradeBookColumnToRemove == $column)
            {
                continue;
            }

            if ($column->getSort() >= $gradeBookColumnToRemove->getSort())
            {
                $column->decrementSort();
            }
        }

        $this->getRemovedEntities()->add($gradeBookColumnToRemove);

        return $this;
    }

    /**
     * @param GradeBookColumn $gradeBookColumn
     * @param int $newSort
     *
     * @return GradeBookData
     */
    public function moveGradeBookColumn(GradeBookColumn $gradeBookColumn, int $newSort): GradeBookData
    {
        if (!$this->gradebookColumns->contains($gradeBookColumn))
        {
            throw new \InvalidArgumentException(
                sprintf('The given column %s is not available in gradebook data %s', $gradeBookColumn->getId(), $this->getId())
            );
        }

        $category = $gradeBookColumn->getGradeBookCategory();

        $oldSort = $gradeBookColumn->getSort();

        foreach ($this->getGradeBookCategoryColumns($category) as $column)
        {
            if ($column == $gradeBookColumn)
            {
                continue;
            }

            if ($column->getSort() >= $oldSort)
            {
                $column->decrementSort();
            }

            if ($column->getSort() >= $newSort)
            {
                $column->incrementSort();
            }
        }

        $gradeBookColumn->setSort($newSort);

        return $this;
    }

    /**
     * @param int $gradeBookColumnId
     * @param int|null $categoryId
     *
     * @return GradeBookColumn
     *
     * @throws ObjectNotExistException
     */
    public function updateGradeBookColumnCategory(int $gradeBookColumnId, ?int $categoryId): GradeBookColumn
    {
        $column = $this->getGradeBookColumnById($gradeBookColumnId);
        $oldCategory = $column->getGradeBookCategory();
        $oldSort = $column->getSort();
        $newCategory = empty($categoryId) ? null : $this->getGradeBookCategoryById($categoryId);

        if ($oldCategory === $newCategory)
        {
            return $column;
        }

        foreach ($this->getGradeBookCategoryColumns($oldCategory) as $gradeBookColumn)
        {
            if ($gradeBookColumn == $column)
            {
                continue;
            }

            if ($gradeBookColumn->getSort() >= $oldSort)
            {
                $gradeBookColumn->decrementSort();
            }
        }

        if ($newCategory instanceof GradeBookCategory)
        {
            $column->setGradeBookCategory($newCategory);
            $column->setSort(count($newCategory->getGradeBookColumns()));
        }
        else
        {
            $column->setGradeBookCategory(null);
            $column->setSort(count($this->getGradeBookColumnsUncategorized()));
        }

        return $column;
    }

    /**
     * @return GradeBookScore[]|ArrayCollection
     */
    public function getGradeBookScores()
    {
        return $this->gradebookScores;
    }

    /**
     * @param GradeBookScore $gradebookScore
     *
     * @return GradeBookData
     */
    public function addGradeBookScore(GradeBookScore $gradebookScore): GradeBookData
    {
        if ($this->gradebookScores->contains($gradebookScore))
        {
            return $this;
        }

        $this->gradebookScores->add($gradebookScore);
        $gradebookScore->setGradeBookData($this);

        return $this;
    }

    /**
     * @param GradeBookScore $gradeBookScoreToRemove
     *
     * @return GradeBookData
     */
    public function removeGradeBookScore(GradeBookScore $gradeBookScoreToRemove): GradeBookData
    {
        if (!$this->gradebookScores->contains($gradeBookScoreToRemove))
        {
            return $this;
        }

        $this->gradebookScores->removeElement($gradeBookScoreToRemove);
        $gradeBookScoreToRemove->setGradeBookData(null);

        $this->getRemovedEntities()->add($gradeBookScoreToRemove);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRemovedEntities(): ?ArrayCollection
    {
        if (!$this->removedEntities instanceof ArrayCollection)
        {
            $this->removedEntities = new ArrayCollection();
        }

        return $this->removedEntities;
    }

    /**
     * @return GradeBookDataJSONModel
     */
    public function toJSONModel(): GradeBookDataJSONModel
    {
        return GradeBookDataJSONModel::fromGradeBookData($this);
    }
}