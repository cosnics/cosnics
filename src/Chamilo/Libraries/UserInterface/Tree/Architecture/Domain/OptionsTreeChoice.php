<?php
namespace Chamilo\Libraries\UserInterface\Tree\Architecture\Domain;

/**
 * @package Chamilo\Libraries\UserInterface\Tree\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OptionsTreeChoice
{
    protected array $attributes;

    protected string $label;

    protected string $value;

    public function __construct(string $value, string $label, array $attributes = [])
    {
        $this->setValue($value);
        $this->setLabel($label);
        $this->setAttributes($attributes);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): OptionsTreeChoice
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): OptionsTreeChoice
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): OptionsTreeChoice
    {
        $this->value = $value;

        return $this;
    }
}