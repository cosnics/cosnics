<?php

namespace Chamilo\Core\Repository\Common\Import\Factory;

/**
 * Class ImportFactories
 * @package Chamilo\Core\Repository\Common\Import\Factory
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ImportFactories
{
    /**
     * @var ImportFactoryInterface[]
     */
    protected $importFactories;

    /**
     * ImportFactories constructor.
     */
    public function __construct()
    {
        $this->importFactories = [];
    }

    /**
     * @return ImportFactoryInterface[]
     */
    public function getImportFactories(): array
    {
        return $this->importFactories;
    }

    /**
     * @param string $alias
     * @param ImportFactoryInterface $importFactory
     *
     * @return ImportFactories
     */
    public function addImportFactory(string $alias, ImportFactoryInterface $importFactory)
    {
        if(array_key_exists($alias, $this->importFactories))
        {
            throw new \RuntimeException(sprintf('An import factory with alias %s has already been added', $alias));
        }
        $this->importFactories[$alias] = $importFactory;

        return $this;
    }

    /**
     * @param string $alias
     *
     * @return ImportFactoryInterface
     */
    public function getImportFactoryByAlias(string $alias)
    {
        if(!array_key_exists($alias, $this->importFactories))
        {
            throw new \RuntimeException(sprintf('Could not find the import factory with alias %s', $alias));
        }

        return $this->importFactories[$alias];
    }

}
