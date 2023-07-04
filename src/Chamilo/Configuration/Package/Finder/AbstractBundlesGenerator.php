<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Finder\Finder;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractBundlesGenerator
{

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
    }

    protected function discoverPackages(string $rootNamespace): array
    {
        $rootNamespace = $rootNamespace == PackageList::ROOT ? '' : $rootNamespace;
        $path = $this->getSystemPathBuilder()->namespaceToFullPath($rootNamespace);

        $finder = new Finder();
        $finder->depth('== 0')->directories()->notName($this->getBlacklistedFolders())->notName('.*')->in($path);

        $packageNamespaces = [];

        foreach ($finder as $folder)
        {
            $folderNamespace = ($rootNamespace ? $rootNamespace . '\\' : '') . $folder->getFilename();

            if ($this->verifyPackage($folderNamespace))
            {
                $packageNamespaces[] = $folderNamespace;
            }

            $packageNamespaces = array_merge($packageNamespaces, $this->discoverPackages($folderNamespace));
        }

        return $packageNamespaces;
    }

    /**
     * @return string[]
     */
    protected function getBlacklistedFolders(): array
    {
        return ['build', 'Build', 'plugin', 'Plugin', 'resources', 'Resources', 'test', 'Test'];
    }

    /**
     * @return string[]
     */
    public function getPackageNamespaces(): array
    {
        return $this->discoverPackages(PackageList::ROOT);
    }

    /**
     * @return string[]
     */
    public function getPackageNamespacesForNamespace(string $namespace = PackageList::ROOT): array
    {
        return $this->discoverPackages($namespace);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    abstract protected function verifyPackage(string $folderNamespace): bool;
}
