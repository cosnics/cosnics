<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\JoinTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
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

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $entityClassName
     */
    public function __construct(
        public PropertyConditionVariable $propertyConditionVariable, public string $entityClassName,
        public ?ConditionInterface $condition = null, public JoinTypeEnum $type = JoinTypeEnum::NORMAL
    )
    {
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = $this->entityClassName;
        $hashParts[] = $this->condition->getHashParts();
        $hashParts[] = $this->type->value;

        return $hashParts;
    }
}
