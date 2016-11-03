<?php
namespace Chamilo\Libraries\Hashing;

use Chamilo\Libraries\Utilities\StringUtilities;

class HashingFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var string
     */
    private $configuredHashingAlgorithm;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param string $configuredHashingAlgorithm
     */
    public function __construct(StringUtilities $stringUtilities, $configuredHashingAlgorithm)
    {
        $this->configuredHashingAlgorithm = $configuredHashingAlgorithm;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredHashingAlgorithm()
    {
        return $this->configuredHashingAlgorithm;
    }

    /**
     *
     * @param string $configuredHashingAlgorithm
     */
    public function setConfiguredHashingAlgorithm($configuredHashingAlgorithm)
    {
        $this->configuredHashingAlgorithm = $configuredHashingAlgorithm;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @throws \Exception
     * @return \Chamilo\Libraries\Hashing\Hashing
     */
    public function getHashingUtilities()
    {
        $className = __NAMESPACE__ . '\\' .
             $this->getStringUtilities()->createString($this->getConfiguredHashingAlgorithm())->upperCamelize();

        if (class_exists($className))
        {
            return new $className();
        }
        else
        {
            throw new \Exception('Hashing algorithm "' . $this->getConfiguredHashingAlgorithm() . '" doesn\'t exist');
        }
    }
}