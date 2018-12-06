<?php

namespace Chamilo\Libraries\Architecture\Bridge;

/**
 * @package Chamilo\Libraries\Architecture\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BridgeManager
{
    /**
     * @var object[]
     */
    protected $bridges;

    /**
     * BridgeManager constructor.
     */
    public function __construct()
    {
        $this->bridges = [];
    }

    /**
     * @param object $bridge
     */
    public function addBridge(object $bridge)
    {
        $interfaces = class_implements($bridge);

        foreach ($interfaces as $bridgeInterface)
        {
            if (array_key_exists($bridgeInterface, $this->bridges))
            {
                throw new \RuntimeException(
                    sprintf('A bridge for the interface %s has already been registered', $bridgeInterface)
                );
            }

            $this->bridges[$bridgeInterface] = $bridge;
        }
    }

    /**
     * @param string $bridgeInterface
     *
     * @return mixed|object
     */
    public function getBridgeByInterface($bridgeInterface)
    {
        if(!array_key_exists($bridgeInterface, $this->bridges))
        {
            throw new \RuntimeException(
                sprintf('A bridge for the interface %s could not be found', $bridgeInterface)
            );
        }

        return $this->bridges[$bridgeInterface];
    }
}