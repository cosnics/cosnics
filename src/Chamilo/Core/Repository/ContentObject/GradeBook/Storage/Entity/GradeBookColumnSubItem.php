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
 *      name="repository_gradebook_column_subitem"
 * )
 */
class GradeBookColumnSubItem
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
     * @var GradeBookColumn
     *
     * @ORM\ManyToOne(targetEntity="GradeBookColumn")
     * @ORM\JoinColumn(name="gradebook_column_id", referencedColumnName="id")
     *
     */
    protected $gradebookColumn;

    /**
     * @var GradeBookItem
     *
     * @ORM\ManyToOne(targetEntity="GradeBookItem")
     * @ORM\JoinColumn(name="gradebook_item_id", referencedColumnName="id")
     *
     */
    protected $gradebookItem;

    /**
     * GradeBookColumnSubItem constructor.
     *
     * @param GradeBookColumn $gradebookColumn
     * @param GradeBookItem $gradebookItem
     *
     */
    public function __construct(GradeBookColumn $gradebookColumn, GradeBookItem $gradebookItem)
    {
        $this->setGradeBookColumn($gradebookColumn);
        $this->setGradeBookItem($gradebookItem);
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
     * @return GradeBookColumnSubItem
     */
    public function setId(int $id): GradeBookColumnSubItem
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return GradeBookColumn
     */
    public function getGradeBookColumn(): ?GradeBookColumn
    {
        return $this->gradebookColumn;
    }

    /**
     * @param ?GradeBookColumn $gradebookColumn
     *
     * @return GradeBookColumnSubItem
     */
    public function setGradeBookColumn(GradeBookColumn $gradebookColumn = null): GradeBookColumnSubItem
    {
        if ($this->gradebookColumn === $gradebookColumn)
        {
            return $this;
        }

        $this->gradebookColumn = $gradebookColumn;

        return $this;
    }

    /**
     * @return GradeBookItem
     */
    public function getGradeBookItem(): ?GradeBookItem
    {
        return $this->gradebookItem;
    }

    /**
     * @param ?GradeBookItem $gradebookItem
     *
     * @return GradeBookColumnSubItem
     */
    public function setGradeBookItem(GradeBookItem $gradebookItem = null): GradeBookColumnSubItem
    {
        if ($this->gradebookItem === $gradebookItem)
        {
            return $this;
        }

        $this->gradebookItem = $gradebookItem;

        return $this;
    }
}
