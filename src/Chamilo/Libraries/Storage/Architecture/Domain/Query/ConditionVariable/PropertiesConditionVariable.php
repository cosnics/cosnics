<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertiesConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private string $dataClassName;

    public function __construct(string $dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\PropertiesConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return PropertiesConditionVariableTranslator::class;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass>
     */
    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function setDataClassName(string $dataClassName): static
    {
        $this->dataClassName = $dataClassName;

        return $this;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getDataClassName()
        ];
    }
}
