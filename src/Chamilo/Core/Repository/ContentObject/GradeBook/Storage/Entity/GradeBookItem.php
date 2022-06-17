<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumb", type="text", nullable=true)
     */
    protected $breadcrumb;

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
        $this->gradebookData = $gradebookData;

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
     * @return string
     */
    public function getBreadcrumb(): ?string
    {
        return $this->breadcrumb;
    }

    /**
     * @param string $breadcrumb
     *
     * @return GradeBookItem
     */
    public function setBreadcrumb(string $breadcrumb): GradeBookItem
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
    public function getContextIdentifier()
    {
        return new ContextIdentifier($this->getContextClass(), $this->getContextId());
    }
}