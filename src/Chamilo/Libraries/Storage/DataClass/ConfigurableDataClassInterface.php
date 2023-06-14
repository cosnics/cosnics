<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConfigurableDataClassInterface
{
    /**
     * @return string[]
     */
    public function getConfiguration(): array;

    /**
     * @param string[] $configuration
     */
    public function setConfiguration(array $configuration): static;

    public function setSetting(string $variable, mixed $value): static;
}