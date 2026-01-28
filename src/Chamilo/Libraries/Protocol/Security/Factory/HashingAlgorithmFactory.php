<?php
namespace Chamilo\Libraries\Protocol\Security\Factory;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;

class HashingAlgorithmFactory
{

    /**
     * @var \Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm[]
     */
    protected array $hashingAlgorithms = [];

    private string $configuredHashingAlgorithm;

    public function __construct(string $configuredHashingAlgorithm)
    {
        $this->configuredHashingAlgorithm = $configuredHashingAlgorithm;
    }

    public function addHashingAlgorithm(HashingAlgorithm $hashingAlgorithm): void
    {
        $this->hashingAlgorithms[get_class($hashingAlgorithm)] = $hashingAlgorithm;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getActiveHashingAlgorithm(): HashingAlgorithm
    {
        $className = $this->getConfiguredHashingAlgorithm();

        if (!isset($this->hashingAlgorithms[$className]))
        {
            throw new ClassNotExistException($className);
        }

        return $this->hashingAlgorithms[$className];
    }

    public function getConfiguredHashingAlgorithm(): string
    {
        return $this->configuredHashingAlgorithm;
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm[]
     */
    public function getHashingAlgorithms(): array
    {
        return $this->hashingAlgorithms;
    }
}