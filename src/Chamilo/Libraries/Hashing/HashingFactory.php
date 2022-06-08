<?php
namespace Chamilo\Libraries\Hashing;

use Chamilo\Libraries\Utilities\StringUtilities;
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

    public function setConfiguredHashingAlgorithm(string $configuredHashingAlgorithm)
    {
        $this->configuredHashingAlgorithm = $configuredHashingAlgorithm;
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

    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }
}