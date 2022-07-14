<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
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
     * GradeBookItemJSONModel constructor.
     *
     * @param int $id
     * @param string $title
     * @param array $breadcrumb
     */
    public function __construct(int $id, string $title, array $breadcrumb)
    {
        $this->id = $id;
        $this->title = $title;
        $this->breadcrumb = $breadcrumb;
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
     * @param GradeBookItem $gradebookItem
     *
     * @return GradeBookItemJSONModel
     */
    public static function fromGradeBookItem(GradeBookItem $gradebookItem)
    {
        return new self(
            $gradebookItem->getId(), $gradebookItem->getTitle(), $gradebookItem->getBreadcrumb()
        );
    }
}
