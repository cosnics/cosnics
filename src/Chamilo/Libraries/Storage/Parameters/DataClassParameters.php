<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataClassParameters implements Hashable
{
    use HashableTrait;

    private ?Condition $condition;

    private ?int $count;

    private bool $distinct;

    private ?GroupBy $groupBy;

    private ?Condition $havingCondition;

    private ?Joins $joins;

    private ?int $offset;

    private ?OrderBy $orderBy;

    private ?RetrieveProperties $retrieveProperties;

    public function __construct(
        ?Condition $condition = null, ?Joins $joins = null, ?RetrieveProperties $retrieveProperties = null,
        ?OrderBy $orderBy = null, ?GroupBy $groupBy = null, ?Condition $havingCondition = null, ?int $count = null,
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
        $hashParts[] = ($this->getJoins() instanceof Joins ? $this->getJoins()->getHashParts() : null);
        $hashParts[] = ($this->getRetrieveProperties() instanceof RetrieveProperties ?
            $this->getRetrieveProperties()->getHashParts() : null);
        $hashParts[] = ($this->getOrderBy() instanceof OrderBy ? $this->getOrderBy()->getHashParts() : null);
        $hashParts[] = ($this->getGroupBy() instanceof GroupBy ? $this->getGroupBy()->getHashParts() : null);
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

    public function getJoins(): ?Joins
    {
        return $this->joins;
    }

    /**
     * Get the offset of the result set relative to the first ordered result
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getOrderBy(): ?OrderBy
    {
        return $this->orderBy;
    }

    public function getRetrieveProperties(): ?RetrieveProperties
    {
        return $this->retrieveProperties;
    }

    /**
     * @deprecated User getCondition() now
     */
    public function get_condition(): ?Condition
    {
        return $this->getCondition();
    }

    /**
     * @deprecated Use getJoins() now
     */
    public function get_joins(): ?Joins
    {
        return $this->getJoins();
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

    public function setJoins(?Joins $joins = null): static
    {
        $this->joins = $joins;

        return $this;
    }

    public function setOffset(?int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function setOrderBy(?OrderBy $orderBy = null): static
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function setRetrieveProperties(?RetrieveProperties $retrieveProperties = null): static
    {
        $this->retrieveProperties = $retrieveProperties;

        return $this;
    }

    /**
     * @deprecated User setCondition() now
     */
    public function set_condition(?Condition $condition = null): static
    {
        return $this->setCondition($condition);
    }

    /**
     * @deprecated Use setJoins() now
     */
    public function set_joins(?Joins $joins = null): static
    {
        return $this->setJoins($joins);
    }
}
