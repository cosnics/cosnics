<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DateFormatConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private ?string $alias;

    private ConditionVariableInterface $conditionVariable;

    private string $format;

    public function __construct(string $format, ConditionVariableInterface $conditionVariable, ?string $alias = null)
    {
        $this->conditionVariable = $conditionVariable;
        $this->format = $format;
        $this->alias = $alias;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getConditionVariable(): ConditionVariableInterface
    {
        return $this->conditionVariable;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\DateFormatConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return DateFormatConditionVariableTranslator::class;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getConditionVariable()->getHashParts(),
            $this->getFormat(),
            $this->getAlias()
        ];
    }
}
