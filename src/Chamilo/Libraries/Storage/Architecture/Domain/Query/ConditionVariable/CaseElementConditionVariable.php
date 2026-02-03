<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseElementConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    /**
     * The condition used after the WHEN statement.
     * If empty the case element is an ELSE statement.
     */
    private ?ConditionInterface $condition;

    private ConditionVariableInterface $statement;

    public function __construct(ConditionVariableInterface $statement, ?ConditionInterface $condition = null)
    {
        $this->statement = $statement;
        $this->condition = $condition;
    }

    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }

    public function setCondition(ConditionInterface $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\CaseElementConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return CaseElementConditionVariableTranslator::class;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;

        if ($this->getCondition() instanceof ConditionInterface) {
            $hashParts[] = $this->getCondition()->hash();
        }
        else {
            $hashParts[] = null;
        }

        $hashParts[] = $this->getStatement()->hash();

        return $hashParts;
    }

    public function getStatement(): ConditionVariableInterface
    {
        return $this->statement;
    }

    public function setStatement(ConditionVariableInterface $statement): static
    {
        $this->statement = $statement;

        return $this;
    }
}
