<?php

namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

/**
 * Class PHPStanPackages
 *
 * @package Chamilo\Libraries\Console\Command\Vendor\PHPStan
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PHPStanPackages
{
    /**
     * @var string[][]
     */
    protected $pathsPerNamespace;

    /**
     * @param array $pathsPerNamespace
     */
    public function setPaths(array $pathsPerNamespace = array())
    {
        foreach ($pathsPerNamespace as $namespace => $paths)
        {
            foreach ($paths as $index => $path)
            {
                $pathsPerNamespace[$namespace][$index] = 'src/' . $path;
            }
        }

        $this->pathsPerNamespace = $pathsPerNamespace;
    }

    /**
     * @param string $namespace
     * @param array $paths
     */
    public function setPathsForNamespace(string $namespace, array $paths = array())
    {
        foreach ($paths as $index => $path)
        {
            $paths[$index] = 'src/' . $path;
        }

        $this->pathsPerNamespace[$namespace] = $paths;
    }

    /**
     * @return array
     */
    public function getAllPaths()
    {
        $allPaths = [];

        foreach ($this->pathsPerNamespace as $paths)
        {
            $allPaths = array_merge($allPaths, $paths);
        }

        return $allPaths;
    }

    /**
     * @param string $namespace
     *
     * @return array|string[]
     */
    public function getPathsForNamespace(string $namespace)
    {
        if (!array_key_exists($namespace, $this->pathsPerNamespace))
        {
            throw new \InvalidArgumentException(
                sprintf('The given namespace %s could not be found in the paths list', $namespace)
            );
        }

        return $this->pathsPerNamespace[$namespace];
    }

    /**
     * @return string[]
     */
    public function getNamespaces()
    {
        return array_keys($this->pathsPerNamespace);
    }
}