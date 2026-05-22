<?php
namespace Chamilo\Core\Group\Storage\Entity;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Types\UuidType;
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
#[Gedmo\Tree(type: 'nested')]
class Group implements DoctrineEntityInterface
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_CODE = 'code';
    public const string PROPERTY_DESCRIPTION = 'description';
    public const string PROPERTY_IDENTIFIER = 'identifier';
    public const string PROPERTY_LEFT_VALUE = 'leftValue';
    public const string PROPERTY_NAME = 'name';
    public const string PROPERTY_PARENT_ID = 'parentIdentifier';
    public const string PROPERTY_RIGHT_VALUE = 'rightValue';

    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['leftValue' => 'ASC'])]
    protected $children;

    #[ORM\Column(name: 'code', type: Types::STRING, nullable: true)]
    protected string $code;

    #[ORM\Column(name: 'description', type: Types::STRING, nullable: true)]
    protected string $description;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[Gedmo\TreeLeft]
    #[ORM\Column(name: 'left_value', type: Types::INTEGER)]
    protected int $leftValue;

    #[ORM\Column(name: 'name', type: Types::STRING)]
    protected string $name;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected $parent;

    #[Gedmo\TreeRight]
    #[ORM\Column(name: 'right_value', type: Types::INTEGER)]
    protected int $rightValue;

    #[Gedmo\TreeLevel]
    #[ORM\Column(name: 'level', type: Types::INTEGER)]
    protected $level;

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: 'root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $root;

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

    public function getParent(): ?Group
    {
        return $this->parent;
    }

    public function setParent(?Group $parent = null): void
    {
        $this->parent = $parent;
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

    public function getRoot(): ?Group
    {
        return $this->root;
    }

    public function hasChildren(): bool
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    public function isRoot(): bool
    {
        return ($this->getParent() == DataClass::EMPTY_UUID);
    }
}
