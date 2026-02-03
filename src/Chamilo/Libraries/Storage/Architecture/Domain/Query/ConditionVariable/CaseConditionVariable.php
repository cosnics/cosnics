<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private ?string $alias;

    /**
     * @var \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable[]
     */
    private array $caseElementConditionVariables;

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable[] $caseElementConditionVariables
     */
    public function __construct(array $caseElementConditionVariables = [], ?string $alias = null)
    {
        $this->caseElementConditionVariables = $caseElementConditionVariables;
        $this->alias = $alias;
    }

    public function add(CaseElementConditionVariable $caseElementConditionVariable): static
    {
        $this->caseElementConditionVariables[] = $caseElementConditionVariable;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable[]
     */
    public function get(): array
    {
        return $this->caseElementConditionVariables;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\CaseConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return CaseConditionVariableTranslator::class;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;

        foreach ($this->get() as $caseElementConditionVariable) {
            $hashParts[] = $caseElementConditionVariable->getHashParts();
        }

        sort($hashParts);

        $hashParts[] = $this->getAlias();

        return $hashParts;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable[] $caseElementConditionVariables
     */
    public function set(array $caseElementConditionVariables): static
    {
        $this->caseElementConditionVariables = $caseElementConditionVariables;

        return $this;
    }
}
