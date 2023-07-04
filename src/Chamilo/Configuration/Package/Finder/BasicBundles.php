<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Finder\Finder;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BasicBundles
{

    /**
     * @var string[]
     */
    private array $packageNamespaces = [];

    private string $rootNamespace;

    public function __construct(string $rootNamespace = PackageList::ROOT)
    {
        $this->rootNamespace = $rootNamespace;
        $this->setup();
    }

    protected function addPackageNamespace(string $packageNamespace): void
    {
        $this->packageNamespaces[] = $packageNamespace;
    }

    private function discoverPackages(string $rootNamespace): void
    {
        $rootNamespace = $rootNamespace == PackageList::ROOT ? '' : $rootNamespace;

        $pathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities('UTF-8')));
        $path = $pathBuilder->namespaceToFullPath($rootNamespace);

        $finder = new Finder();
        $finder->depth('== 0')->directories()->notName($this->getBlacklistedFolders())->notName('.*')->in($path);

        foreach ($finder as $folder)
        {
            $folderNamespace = ($rootNamespace ? $rootNamespace . '\\' : '') . $folder->getFilename();

            if ($this->verifyPackage($folderNamespace))
            {
                $this->addPackageNamespace($folderNamespace);
            }

            $this->discoverPackages($folderNamespace);
        }
    }

    /**
     * @return string[]
     */
    protected function getBlacklistedFolders(): array
    {
        return ['.hg', '.git', 'build', 'Build', 'plugin', 'resources', 'Resources', 'Test'];
    }

    /**
     * @return string[]
     */
    public function getPackageNamespaces(): array
    {
        return $this->packageNamespaces;
    }

    public function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

    protected function setup(): void
    {
        $this->discoverPackages($this->rootNamespace);
    }

    protected function verifyPackage(string $folderNamespace): bool
    {
        $pathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities('UTF-8')));
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/composer.json';

        return file_exists($packageInfoPath);
    }
}
