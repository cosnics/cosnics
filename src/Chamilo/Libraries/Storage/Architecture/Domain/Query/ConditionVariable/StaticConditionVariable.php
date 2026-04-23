<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\StaticConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private bool $quote;

    private mixed $value;

    public function __construct($value, ?bool $quote = true)
    {
        $this->value = $value;
        $this->quote = $quote;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\StaticConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return StaticConditionVariableTranslator::class;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getValue(),
            $this->getQuote()
        ];
    }

    public function getQuote(): bool
    {
        return $this->quote;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
