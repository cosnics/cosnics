<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonAttributesTrait
{
    /**
     * @var string[][]
     */
    private array $attributes = [];

    /**
     * @param string[] $value
     */
    public function addAttribute(string $name, array $value): static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string[][] $attributes
     */
    public function addAttributes(array $attributes = []): static
    {
        $this->attributes = array_merge_recursive($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAttribute(string $name): array
    {
        return $this->attributes[$name] ?? [];
    }

    /**
     * @return string[][]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string[][] $attributes
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }
}
