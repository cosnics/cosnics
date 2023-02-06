<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookCategory;
use Doctrine\Common\Collections\ArrayCollection;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookDataJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $dataId;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $version;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $title;

    /**
     * @var GradeBookItemJSONModel[]
     */
    protected $gradeItems;

    /**
     * @var GradeBookColumnJSONModel[]
     */
    protected $gradeColumns;

    /**
     * @var GradeBookCategoryJSONModel[]
     */
    protected $categories;

    /**
     * @var GradeBookCategoryJSONModel
     */
    protected $nullCategory;

    /**
     * @param int $id
     * @param int $version
     * @param string $title
     * @param GradeBookItemJSONModel[] $gradeItems
     * @param GradeBookColumnJSONModel[] $gradeColumns
     * @param GradeBookCategoryJSONModel[] $categories
     * @param GradeBookCategoryJSONModel $nullCategory
     */
    public function __construct(int $id, int $version, string $title, array $gradeItems, array $gradeColumns, array $categories, GradeBookCategoryJSONModel $nullCategory)
    {
        $this->dataId = $id;
        $this->version = $version;
        $this->title = $title;
        $this->gradeItems = $gradeItems;
        $this->gradeColumns = $gradeColumns;
        $this->categories = $categories;
        $this->nullCategory = $nullCategory;
    }

    /**
     * @return int
     */
    public function getDataId(): ?int
    {
        return $this->dataId;
    }

    /**
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return GradeBookItemJSONModel[]
     */
    public function getGradeItems(): array
    {
        return $this->gradeItems;
    }

    /**
     * @return GradeBookColumnJSONModel[]
     */
    public function getGradeColumns(): array
    {
        return $this->gradeColumns;
    }

    /**
     * @return GradeBookCategoryJSONModel[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return array|GradeBookCategoryJSONModel
     */
    public function getNullCategory(): array
    {
        return $this->nullCategory;
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookDataJSONModel
     */
    public static function fromGradeBookData(GradeBookData $gradebookData)
    {
        $gradebookItems = array_map(function (GradeBookItem $gradebookItem) {
            return $gradebookItem->toJSONModel();
        }, $gradebookData->getGradeBookItems()->toArray());

        $gradebookColumns = array_map(function (GradeBookColumn $gradebookColumn) {
            return $gradebookColumn->toJSONModel();
        }, $gradebookData->getGradeBookColumns()->toArray());

        $categories = array_map(function (GradeBookCategory $gradebookCategory) {
            return $gradebookCategory->toJSONModel();
        }, $gradebookData->getGradeBookCategories()->toArray());

        $nullCategory = GradeBookCategory::nullCategoryToJSONModel($gradebookData->getGradeBookColumnsUncategorized());

        return new self(
            $gradebookData->getId(), $gradebookData->getVersion(), $gradebookData->getTitle(), $gradebookItems, $gradebookColumns, $categories, $nullCategory
        );
    }
}