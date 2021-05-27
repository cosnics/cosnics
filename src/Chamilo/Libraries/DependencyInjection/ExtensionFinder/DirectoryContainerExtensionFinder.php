<?php
namespace Chamilo\Libraries\DependencyInjection\ExtensionFinder;

use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Symfony\Component\Finder\Finder;

/**
 * Finds all dependency injection extensions in a directory structure from a given root directory
 *
 * @package Chamilo\Libraries\DependencyInjection\ExtensionFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DirectoryContainerExtensionFinder implements ContainerExtensionFinderInterface
{

    /**
     * The root directory
     *
     * @var string
     */
    private $rootDirectory;

    /**
     * The Symfony Finder component
     *
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * Constructor
     *
     * @param string $rootDirectory
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct($rootDirectory, Finder $finder = null)
    {
        $this->rootDirectory = $rootDirectory;

        if (is_null($finder))
        {
            $finder = new Finder();
        }

        $this->finder = $finder;
    }

    /**
     * Locates the container extension classes
     *
     * @return string[]
     */
    public function findContainerExtensions()
    {
        $this->finder->files()->in($this->rootDirectory)->notPath('Plugin')->notPath('Resources')->path(
            '/DependencyInjection\//'
        )->name('DependencyInjectionExtension.php');

        $containerExtensionClasses = [];

        foreach ($this->finder as $file)
        {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $namespace = str_replace('/', '\\', $file->getRelativePath());
            $containerExtensionClasses[] = $namespace . '\\DependencyInjectionExtension';
        }

        return $containerExtensionClasses;
    }
}