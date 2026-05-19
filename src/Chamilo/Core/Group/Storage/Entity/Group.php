<?php
namespace Chamilo\Core\Group\Storage\Entity;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Sven Vanpoucke
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\Group\Storage\Repository\GroupEntityRepository')]
#[ORM\Table(name: 'group_group')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class Group implements DoctrineEntityInterface
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_CODE = 'code';
    public const string PROPERTY_DESCRIPTION = 'description';
    public const string PROPERTY_LEFT_VALUE = 'leftValue';
    public const string PROPERTY_NAME = 'name';
    public const string PROPERTY_PARENT_ID = 'parentIdentifier';
    public const string PROPERTY_RIGHT_VALUE = 'rightValue';

    #[ORM\Column(name: 'code', type: 'string', nullable: true)]
    protected string $code;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    protected string $description;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'left_value', type: 'integer')]
    protected int $leftValue;

    #[ORM\Column(name: 'name', type: 'string')]
    protected string $name;

    #[ORM\Column(name: 'parent_id', type: 'uuid')]
    protected Uuid $parentIdentifier;

    #[ORM\Column(name: 'right_value', type: 'integer')]
    protected int $rightValue;

    public static function getAlias(): string
    {
        return 't_grp_grp';
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getLeftValue(): int
    {
        return $this->leftValue;
    }

    public function setLeftValue(int $leftValue): static
    {
        $this->leftValue = $leftValue;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getParentIdentifier(): Uuid
    {
        return $this->parentIdentifier;
    }

    public function setParentIdentifier(Uuid $parentIdentifier): static
    {
        $this->parentIdentifier = $parentIdentifier;

        return $this;
    }

    public function getRightValue(): int
    {
        return $this->rightValue;
    }

    public function setRightValue(int $rightValue): static
    {
        $this->rightValue = $rightValue;

        return $this;
    }

    public function hasChildren(): bool
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    public function isRoot(): bool
    {
        return ($this->getParentIdentifier() == DataClass::EMPTY_UUID);
    }
}
