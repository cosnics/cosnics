<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\JoinTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Join implements HashableInterface
{
    use HashableTrait;

    private ?ConditionInterface $condition;

    private string $dataClassName;

    private JoinTypeEnum $type;

    public function __construct(
        string $dataClassName, ?ConditionInterface $condition = null, JoinTypeEnum $type = JoinTypeEnum::NORMAL
    )
    {
        $this->dataClassName = $dataClassName;
        $this->condition = $condition;
        $this->type = $type;
    }

    public function getCondition(): ConditionInterface
    {
        return $this->condition;
    }

    public function setCondition(?ConditionInterface $condition = null): static
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass>
     */
    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = $this->getDataClassName();
        $hashParts[] = $this->getCondition()->getHashParts();
        $hashParts[] = $this->getType()->value;

        return $hashParts;
    }

    public function getType(): JoinTypeEnum
    {
        return $this->type;
    }

    public function setType(JoinTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }
}
