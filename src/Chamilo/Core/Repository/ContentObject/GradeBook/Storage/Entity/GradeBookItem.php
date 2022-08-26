<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Libraries\Architecture\ContextIdentifier;
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
 *      name="repository_gradebook_item",
 *      indexes={
 *          @ORM\Index(name="rgi_context", columns={"context_class", "context_id"}),
 *      }
 * )
 */
class GradeBookItem
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
     * @var GradeBookColumn
     *
     * @ORM\ManyToOne(targetEntity="GradeBookColumn")
     * @ORM\JoinColumn(name="gradebook_column_id", referencedColumnName="id")
     *
     */
    protected $gradebookColumn;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var array
     */
    protected $breadcrumb = array();

    /**
     * @var string
     *
     * @ORM\Column(name="context_class", type="string")
     */
    protected $contextClass;

    /**
     * @var int
     *
     * @ORM\Column(name="context_id", type="integer")
     */
    protected $contextId;

    /**
     * GradeBookItem constructor.
     *
     * @param GradeBookData|null $gradebookData
     * @param GradeBookColumn|null $gradebookColumn
     */
    public function __construct(GradeBookData $gradebookData = null, GradeBookColumn $gradebookColumn = null)
    {
        $this->setGradeBookData($gradebookData);
        $this->setGradeBookColumn($gradebookColumn);
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
     * @return GradeBookItem
     */
    public function setId(int $id): GradeBookItem
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
     * @return GradeBookItem
     */
    public function setGradeBookData(GradeBookData $gradebookData = null): GradeBookItem
    {
        if ($this->gradebookData === $gradebookData)
        {
            return $this;
        }

        $oldGradebookData = $this->gradebookData;
        $this->gradebookData = $gradebookData;

        if ($oldGradebookData instanceof GradeBookData)
        {
            $oldGradebookData->removeGradeBookItem($this);
        }

        if ($gradebookData instanceof GradeBookData)
        {
            $gradebookData->addGradeBookItem($this);
        }

        return $this;
    }

    /**
     * @return GradeBookColumn|null
     */
    public function getGradeBookColumn(): ?GradeBookColumn {
        return $this->gradebookColumn;
    }

    /**
     * @param GradeBookColumn|null $gradebookColumn
     *
     * @return GradeBookItem
     */
    public function setGradeBookColumn(GradeBookColumn $gradebookColumn = null): GradeBookItem
    {
        if ($this->gradebookColumn === $gradebookColumn)
        {
            return $this;
        }

        $oldGradebookColumn = $this->gradebookColumn;
        $this->gradebookColumn = $gradebookColumn;

        if ($oldGradebookColumn instanceof GradeBookColumn)
        {
            $oldGradebookColumn->removeGradeBookColumnSubItem($this);
        }

        if ($gradebookColumn instanceof GradeBookColumn)
        {
            $gradebookColumn->addGradeBookColumnSubItem($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return GradeBookItem
     */
    public function setType(string $type): GradeBookItem
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
     * @param string $title
     *
     * @return GradeBookItem
     */
    public function setTitle(string $title): GradeBookItem
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): ?array
    {
        return $this->breadcrumb;
    }

    /**
     * @param array $breadcrumb
     *
     * @return GradeBookItem
     */
    public function setBreadcrumb(array $breadcrumb): GradeBookItem
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @return string
     */
    public function getContextClass(): ?string
    {
        return $this->contextClass;
    }

    /**
     * @param string $contextClass
     *
     * @return GradeBookItem
     */
    public function setContextClass(string $contextClass): GradeBookItem
    {
        $this->contextClass = $contextClass;

        return $this;
    }

    /**
     * @return int
     */
    public function getContextId(): ?int
    {
        return $this->contextId;
    }

    /**
     * @param int $contextId
     *
     * @return GradeBookItem
     */
    public function setContextId(int $contextId): GradeBookItem
    {
        $this->contextId = $contextId;

        return $this;
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     *
     * @return $this
     */
    public function setContextIdentifier(ContextIdentifier $contextIdentifier)
    {
        $this->setContextClass($contextIdentifier->getContextClass());
        $this->setContextId($contextIdentifier->getContextId());

        return $this;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return new ContextIdentifier($this->getContextClass(), $this->getContextId());
    }

    /**
     * @return GradeBookItemJSONModel
     */
    public function toJSONModel(): GradeBookItemJSONModel
    {
        return GradeBookItemJSONModel::fromGradeBookItem($this);
    }
}