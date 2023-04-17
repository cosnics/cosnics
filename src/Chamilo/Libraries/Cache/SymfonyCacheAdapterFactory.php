<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SymfonyCacheAdapterFactory
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function createArrayAdapter(int $defaultLifetime = 0): ArrayAdapter
    {
        return new ArrayAdapter($defaultLifetime);
    }

    public function createFilesystemAdapter(string $namespace, int $defaultLifetime = 0): FilesystemAdapter
    {
        return new FilesystemAdapter(
            md5($namespace), $defaultLifetime, $this->getConfigurablePathBuilder()->getCachePath()
        );
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createPhpFilesAdapter(string $namespace, int $defaultLifetime = 0): PhpFilesAdapter
    {
        return new PhpFilesAdapter(
            md5($namespace), $defaultLifetime, $this->getConfigurablePathBuilder()->getCachePath()
        );
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

}