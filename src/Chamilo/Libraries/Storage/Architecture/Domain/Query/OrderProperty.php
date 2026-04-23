<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OrderProperty implements HashableInterface
{
    use HashableTrait;

    private StaticConditionVariable|PropertyConditionVariable $conditionVariable;

    private int $direction;

    public function __construct(
        StaticConditionVariable|PropertyConditionVariable $conditionVariable, ?int $direction = SORT_ASC
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->direction = $direction;
    }

    public function getConditionVariable(): StaticConditionVariable|PropertyConditionVariable
    {
        return $this->conditionVariable;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function getHashParts(): array
    {
        return [$this->getConditionVariable()->getHashParts(), $this->getDirection()];
    }
}
