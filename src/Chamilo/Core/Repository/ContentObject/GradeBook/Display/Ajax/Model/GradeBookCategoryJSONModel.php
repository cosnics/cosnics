<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookCategory;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Doctrine\Common\Collections\ArrayCollection;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookCategoryJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $color;

    /**
     * @var int[]
     *
     */
    protected $columnIds;

    /**
     * GradeBookCategoryJSONModel constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $color
     * @param GradeBookColumn[]|ArrayCollection $columns
     */
    public function __construct(int $id, string $title, string $color, $columns)
    {
        $this->id = $id;
        $this->title = $title;
        $this->color = $color;

        $this->columnIds = array();
        foreach ($columns as $column)
        {
            $this->columnIds[] = $column->getId();
        }
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @return int[]
     */
    public function getColumnIds(): array
    {
        return $this->columnIds;
    }

    /**
     * @param GradeBookCategory $category
     *
     * @return GradeBookCategory
     */
    public function updateGradeBookCategory(GradeBookCategory $category)
    {
        $category->setId($this->id);
        $category->setTitle($this->title);
        $category->setColor($this->color);

        return $category;
    }

    /**
     * @param GradeBookCategory $gradebookCategory
     *
     * @return GradeBookCategoryJSONModel
     */
    public static function fromGradeBookCategory(GradeBookCategory $gradebookCategory)
    {
        return new self(
            $gradebookCategory->getId(), $gradebookCategory->getTitle(), $gradebookCategory->getColor(), $gradebookCategory->getGradeBookColumns()
        );
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookCategory
     */
    public function toGradeBookCategory(GradeBookData $gradebookData)
    {
        $category = new GradeBookCategory($gradebookData);
        $this->updateGradeBookCategory($category);

        return $category;
    }
}
