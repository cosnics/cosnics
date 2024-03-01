<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataClassParameters implements HashableInterface
{
    use HashableTrait;

    private ?Condition $condition;

    private ?int $count;

    private GroupBy $groupBy;

    private ?Condition $havingCondition;

    private Joins $joins;

    private ?int $offset;

    private OrderBy $orderBy;

    private RetrieveProperties $retrieveProperties;

    public function __construct(
        ?Condition $condition = null, Joins $joins = new Joins(),
        RetrieveProperties $retrieveProperties = new RetrieveProperties(), OrderBy $orderBy = new OrderBy(),
        GroupBy $groupBy = new GroupBy(), ?Condition $havingCondition = null, ?int $count = null, ?int $offset = null
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

    public function addConditionUsingAnd(?Condition $condition = null): static
    {
        if ($condition instanceof Condition)
        {
            if ($this->getCondition() instanceof Condition)
            {
                $this->setCondition(new AndCondition([$this->getCondition(), $condition]));
            }
            else
            {
                $this->setCondition($condition);
            }
        }

        return $this;
    }

    public function addConditionUsingOr(?Condition $condition = null): static
    {
        if ($condition instanceof Condition)
        {
            if ($this->getCondition() instanceof Condition)
            {
                $this->setCondition(new OrCondition([$this->getCondition(), $condition]));
            }
            else
            {
                $this->setCondition($condition);
            }
        }

        return $this;
    }

    public function addJoin(?Join $join = null): static
    {
        if ($join instanceof Join)
        {
            $this->getJoins()->add($join);
        }

        return $this;
    }

    public function getCondition(): ?Condition
    {
        return $this->condition;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function getGroupBy(): ?GroupBy
    {
        return $this->groupBy;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;
        $hashParts[] = ($this->getCondition() instanceof Condition ? $this->getCondition()->getHashParts() : null);
        $hashParts[] = $this->getJoins()->getHashParts();
        $hashParts[] = $this->getRetrieveProperties()->getHashParts();
        $hashParts[] = $this->getOrderBy()->getHashParts();
        $hashParts[] = $this->getGroupBy()->getHashParts();
        $hashParts[] =
            ($this->getHavingCondition() instanceof Condition ? $this->getHavingCondition()->getHashParts() : null);
        $hashParts[] = $this->getCount();
        $hashParts[] = $this->getOffset();

        return $hashParts;
    }

    public function getHavingCondition(): ?Condition
    {
        return $this->havingCondition;
    }

    public function getJoins(): Joins
    {
        return $this->joins;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getOrderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function getRetrieveProperties(): RetrieveProperties
    {
        return $this->retrieveProperties;
    }

    public function returnSingleResult(): static
    {
        $this->setCount(1);
        $this->setOffset(0);

        return $this;
    }

    public function setCondition(?Condition $condition = null): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function setCount(?int $count): static
    {
        $this->count = (int) $count;

        return $this;
    }

    public function setGroupBy(?GroupBy $groupBy = null): static
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function setHavingCondition(?Condition $havingCondition = null): static
    {
        $this->havingCondition = $havingCondition;

        return $this;
    }

    public function setJoins(Joins $joins = new Joins()): static
    {
        $this->joins = $joins;

        return $this;
    }

    public function setOffset(?int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function setOrderBy(OrderBy $orderBy = new OrderBy()): static
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function setRetrieveProperties(RetrieveProperties $retrieveProperties = new RetrieveProperties()): static
    {
        $this->retrieveProperties = $retrieveProperties;

        return $this;
    }
}
