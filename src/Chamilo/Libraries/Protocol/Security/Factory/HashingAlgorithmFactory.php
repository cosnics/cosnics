<?php
namespace Chamilo\Libraries\Protocol\Security\Factory;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
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

    public function __construct(protected string $configuredHashingAlgorithm)
    {
    }

    public function addHashingAlgorithm(HashingAlgorithm $hashingAlgorithm): void
    {
        $this->hashingAlgorithms[get_class($hashingAlgorithm)] = $hashingAlgorithm;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getActiveHashingAlgorithm(): HashingAlgorithm
    {
        if (!isset($this->hashingAlgorithms[$this->configuredHashingAlgorithm])) {
            throw new NoSuchClassException(
                $this->configuredHashingAlgorithm, HashingAlgorithm::class
            );
        }

        return $this->hashingAlgorithms[$this->configuredHashingAlgorithm];
    }
}