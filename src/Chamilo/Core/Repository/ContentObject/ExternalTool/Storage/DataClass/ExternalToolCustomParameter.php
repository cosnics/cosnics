<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 */
class ExternalToolCustomParameter
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;

    /**
     * ExternalToolCustomParameter constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['name' => $this->name, 'value' => $this->value];
    }

    /**
     * @param array $customParameter
     *
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalToolCustomParameter
     */
    public static function fromArray(array $customParameter)
    {
        return new ExternalToolCustomParameter($customParameter['name'], $customParameter['value']);
    }
}