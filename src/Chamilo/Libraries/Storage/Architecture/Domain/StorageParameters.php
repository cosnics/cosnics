<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\GroupBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Join;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class StorageParameters implements HashableInterface
{
    use HashableTrait;

    private ?ConditionInterface $condition;

    private ?int $count;

    private GroupBy $groupBy;

    private ?ConditionInterface $havingCondition;

    private Joins $joins;

    private ?int $offset;

    private OrderBy $orderBy;

    private RetrieveProperties $retrieveProperties;

    public function __construct(
        ?ConditionInterface $condition = null, Joins $joins = new Joins(),
        RetrieveProperties $retrieveProperties = new RetrieveProperties(), OrderBy $orderBy = new OrderBy(),
        GroupBy $groupBy = new GroupBy(), ?ConditionInterface $havingCondition = null, ?int $count = null,
        ?int $offset = null
    )
    {
        $this->setCondition($condition);
        $this->setJoins($joins);
        $this->setRetrieveProperties($retrieveProperties);
        $this->setOrderBy($orderBy);
        $this->setGroupBy($groupBy);
        $this->setHavingCondition($havingCondition);
        $this->setCount($count);
        $this->setOffset($offset);
    }

    public function addConditionUsingAnd(?ConditionInterface $condition = null): static
    {
        if ($condition instanceof ConditionInterface) {
            if ($this->getCondition() instanceof ConditionInterface) {
                $this->setCondition(new AndCondition([$this->getCondition(), $condition]));
            }
            else {
                $this->setCondition($condition);
            }
        }

        return $this;
    }

    public function addConditionUsingOr(?ConditionInterface $condition = null): static
    {
        if ($condition instanceof ConditionInterface) {
            if ($this->getCondition() instanceof ConditionInterface) {
                $this->setCondition(new OrCondition([$this->getCondition(), $condition]));
            }
            else {
                $this->setCondition($condition);
            }
        }

        return $this;
    }

    public function addJoin(?Join $join = null): static
    {
        if ($join instanceof Join) {
            $this->getJoins()->add($join);
        }

        return $this;
    }

    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }

    public function setCondition(?ConditionInterface $condition = null): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = (int) $count;

        return $this;
    }

    public function getGroupBy(): ?GroupBy
    {
        return $this->groupBy;
    }

    public function setGroupBy(?GroupBy $groupBy = null): static
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;
        $hashParts[] =
            ($this->getCondition() instanceof ConditionInterface ? $this->getCondition()->getHashParts() : null);
        $hashParts[] = $this->getJoins()->getHashParts();
        $hashParts[] = $this->getRetrieveProperties()->getHashParts();
        $hashParts[] = $this->getOrderBy()->getHashParts();
        $hashParts[] = $this->getGroupBy()->getHashParts();
        $hashParts[] =
            ($this->getHavingCondition() instanceof ConditionInterface ? $this->getHavingCondition()->getHashParts() :
                null);
        $hashParts[] = $this->getCount();
        $hashParts[] = $this->getOffset();

        return $hashParts;
    }

    public function getHavingCondition(): ?ConditionInterface
    {
        return $this->havingCondition;
    }

    public function setHavingCondition(?ConditionInterface $havingCondition = null): static
    {
        $this->havingCondition = $havingCondition;

        return $this;
    }

    public function getJoins(): Joins
    {
        return $this->joins;
    }

    public function setJoins(Joins $joins = new Joins()): static
    {
        $this->joins = $joins;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOrderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function setOrderBy(OrderBy $orderBy = new OrderBy()): static
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getRetrieveProperties(): RetrieveProperties
    {
        return $this->retrieveProperties;
    }

    public function setRetrieveProperties(RetrieveProperties $retrieveProperties = new RetrieveProperties()): static
    {
        $this->retrieveProperties = $retrieveProperties;

        return $this;
    }

    public function returnSingleResult(): static
    {
        $this->setCount(1);
        $this->setOffset(0);

        return $this;
    }
}
