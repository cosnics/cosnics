<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\Condition\NotConditionTranslator;

/**
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class NotCondition implements ConditionInterface
{
    use HashableTrait;

    private ConditionInterface $condition;

    public function __construct(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    public function getCondition(): ConditionInterface
    {
        return $this->condition;
    }

    public function getConditionTranslatorClass(): string
    {
        return NotConditionTranslator::class;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getCondition()->getHashParts()
        ];
    }
}
