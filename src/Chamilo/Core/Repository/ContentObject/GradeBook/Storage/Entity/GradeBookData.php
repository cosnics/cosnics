<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

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
     * @return GradeBookColumn[]|ArrayCollection
     */
    public function getGradeBookColumns()
    {
        return $this->gradebookColumns;
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
     * @return GradeBookCategory[]|ArrayCollection
     */
    public function getGradeBookCategories()
    {
        return $this->gradebookCategories;
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
}