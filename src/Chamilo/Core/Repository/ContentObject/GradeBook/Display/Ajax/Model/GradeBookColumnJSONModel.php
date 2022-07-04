<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Doctrine\Common\Collections\ArrayCollection;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookColumnJSONModel
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
    protected $type;

    /**
     * @var string|null
     *
     * @Type("string")
     */
    protected $title;

    /**
     * @var int|null
     *
     * @Type("integer")
     */
    protected $weight;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $countForEndResult;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $authPresenceEndResult;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $unauthPresenceEndResult;

    /**
     * @var int[]
     *
     * @Type("array")
     */
    protected $subItemIds;


    /**
     * GradeBookColumnJSONModel constructor.
     *
     * @param int $id
     * @param string $type
     * @param ?string $title
     * @param ?int $weight
     * @param bool $countForEndResult
     * @param int $authPresenceEndResult
     * @param int $unauthPresenceEndResult
     * @param GradeBookItem[]|ArrayCollection|array $subItems
     */
    public function __construct(int $id, string $type, ?string $title, ?int $weight, bool $countForEndResult, int $authPresenceEndResult, int $unauthPresenceEndResult, $subItems)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->weight = $weight;
        $this->countForEndResult = $countForEndResult;
        $this->authPresenceEndResult = $authPresenceEndResult;
        $this->unauthPresenceEndResult = $unauthPresenceEndResult;
        $this->subItemIds = array();
        foreach ($subItems as $item)
        {
            $this->subItemIds[] = $item->getId();
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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @return bool
     */
    public function getCountForEndResult(): ?bool
    {
        return $this->countForEndResult;
    }

    /**
     * @return int
     */
    public function getAuthPresenceEndResult(): ?int
    {
        return $this->authPresenceEndResult;
    }

    /**
     * @return int
     */
    public function getUnauthPresenceEndResult(): ?int
    {
        return $this->unauthPresenceEndResult;
    }

    /**
     * @return int[]
     */
    public function getSubItemIds(): array
    {
        return $this->subItemIds;
    }

    /**
     * @param GradeBookColumn $gradebookColumn
     *
     * @return GradeBookColumnJSONModel
     */
    public static function fromGradeBookColumn(GradeBookColumn $gradebookColumn)
    {
        return new self(
            $gradebookColumn->getId(), $gradebookColumn->getType(), $gradebookColumn->getTitle(),
            $gradebookColumn->getWeight(), $gradebookColumn->getCountForEndResult(),
            $gradebookColumn->getAuthPresenceEndResult(), $gradebookColumn->getUnauthPresenceEndResult(),
            $gradebookColumn->getGradeBookColumnSubItems()
        );
    }

    /**
     * @param GradeBookColumn $column
     *
     * @return GradeBookColumn
     */
    public function updateGradeBookColumn(GradeBookColumn $column)
    {
        $column->setId($this->id);
        $column->setType($this->type);
        $column->setTitle($this->title);
        $column->setWeight($this->weight);
        $column->setCountForEndResult($this->countForEndResult);
        $column->setAuthPresenceEndResult($this->authPresenceEndResult);
        $column->setUnauthPresenceEndResult($this->unauthPresenceEndResult);

        return $column;
    }
}
