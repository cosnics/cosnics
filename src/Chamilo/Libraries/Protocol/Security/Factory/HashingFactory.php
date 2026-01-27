<?php
namespace Chamilo\Libraries\Protocol\Security\Factory;

use Chamilo\Libraries\Protocol\Security\Service\HashingUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;

class HashingFactory
{

    private string $configuredHashingAlgorithm;

    private StringUtilities $stringUtilities;

    public function __construct(StringUtilities $stringUtilities, string $configuredHashingAlgorithm)
    {
        $this->stringUtilities = $stringUtilities;
        $this->configuredHashingAlgorithm = $configuredHashingAlgorithm;
    }

    public function getConfiguredHashingAlgorithm(): string
    {
        return $this->configuredHashingAlgorithm;
    }

    /**
     * @throws \Exception
     */
    public function getHashingUtilities(): HashingUtilities
    {
        $className = __NAMESPACE__ . '\Type\\' .
            $this->getStringUtilities()->createString($this->getConfiguredHashingAlgorithm())->upperCamelize() .
            'Utilities';

        if (class_exists($className))
        {
            return new $className();
        }
        else
        {
            throw new Exception('Hashing algorithm "' . $this->getConfiguredHashingAlgorithm() . '" doesn\'t exist');
        }
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }
}