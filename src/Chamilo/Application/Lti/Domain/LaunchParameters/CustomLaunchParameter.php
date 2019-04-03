<?php

namespace Chamilo\Application\Lti\Domain\LaunchParameters;

/**
 * Class CustomLaunchParameter
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class CustomLaunchParameter
{
    /**
     * @var string
     */
    protected $keyName;

    /**
     * @var string
     */
    protected $value;

    /**
     * CustomLaunchParameter constructor.
     *
     * @param string $keyName
     * @param string $value
     */
    public function __construct(string $keyName, string $value)
    {
        $keyName = str_replace(' ', '_', $keyName);
        $keyName = str_replace(':', '_', $keyName);
        $keyName = strtolower($keyName);

        $this->keyName = $keyName;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}