<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookItemJSONModel
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
     * @var int[]
     *
     * @Type("array")
     */
    protected $breadcrumb;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $removed;

    /**
     * GradeBookItemJSONModel constructor.
     *
     * @param int $id
     * @param string $title
     * @param array $breadcrumb
     * @param bool $removed
     */
    public function __construct(int $id, string $title, array $breadcrumb, bool $removed)
    {
        $this->id = $id;
        $this->title = $title;
        $this->breadcrumb = $breadcrumb;
        $this->removed = $removed;
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
     * @return array
     */
    public function getBreadcrumb(): ?array
    {
        return $this->breadcrumb;
    }

    /**
     * @return bool
     */
    public function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * @param GradeBookItem $gradebookItem
     *
     * @return GradeBookItemJSONModel
     */
    public static function fromGradeBookItem(GradeBookItem $gradebookItem)
    {
        return new self(
            $gradebookItem->getId(), $gradebookItem->getTitle(), $gradebookItem->getBreadcrumb(), $gradebookItem->isRemoved()
        );
    }
}
