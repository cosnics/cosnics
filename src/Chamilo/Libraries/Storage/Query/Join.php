<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * This class describes a storage unit you want to join with
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Join implements Hashable
{
    use HashableTrait;

    const TYPE_LEFT = 2;
    const TYPE_NORMAL = 1;
    const TYPE_RIGHT = 3;

    private ?Condition $condition;

    private string $dataClassName;

    private int $type;

    public function __construct(string $dataClassName, ?Condition $condition = null, ?int $type = self::TYPE_NORMAL)
    {
        $this->dataClassName = $dataClassName;
        $this->condition = $condition;
        $this->type = $type;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function setCondition(?Condition $condition = null)
    {
        $this->condition = $condition;
    }

    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function setDataClassName(string $dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = $this->getDataClassName();
        $hashParts[] = $this->getCondition()->getHashParts();
        $hashParts[] = $this->getType();

        return $hashParts;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type)
    {
        $this->type = $type;
    }
}
