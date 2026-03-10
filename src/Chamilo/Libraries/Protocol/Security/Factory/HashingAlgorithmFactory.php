<?php
namespace Chamilo\Libraries\Protocol\Security\Factory;

use Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;

/**
 * @package Chamilo\Libraries\Protocol\Security\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
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
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function getActiveHashingAlgorithm(): HashingAlgorithm
    {
        $className = $this->getConfiguredHashingAlgorithm();

        if (!isset($this->hashingAlgorithms[$className])) {
            throw new NoSuchClassException(
                $className, HashingAlgorithm::class
            );
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