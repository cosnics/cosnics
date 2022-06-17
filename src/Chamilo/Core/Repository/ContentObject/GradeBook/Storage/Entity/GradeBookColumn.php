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
     * @var GradeBookColumnSubItem[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GradeBookColumnSubItem", mappedBy="gradebookColumn")
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
        $this->gradebookData = $gradebookData;

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
     * @return GradeBookColumnSubItem[]|ArrayCollection
     */
    public function getGradeBookColumnSubItems()
    {
        return $this->gradebookColumnSubItems;
    }

    /**
     * @return array
     */
    public function getSubItems(): array
    {
        $items = [];
        foreach ($this->getGradeBookColumnSubItems() as $subItem)
        {
            $items[] = $subItem->getGradeBookItem();
        }
        return $items;
    }
}