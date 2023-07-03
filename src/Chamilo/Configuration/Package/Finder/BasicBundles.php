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
    private $packageNamespaces = [];

    /**
     * @var string
     */
    private $rootNamespace;

    /**
     * @param string $namespace
     * @param int $mode
     */
    public function __construct($rootNamespace = PackageList::ROOT)
    {
        $this->rootNamespace = $rootNamespace;
        $this->setup();
    }

    /**
     * @param string $packageNamespace
     */
    protected function addPackageNamespace($packageNamespace)
    {
        $this->packageNamespaces[] = $packageNamespace;
    }

    /**
     * @param string $namespace
     */
    private function discoverPackages($rootNamespace)
    {
        $blacklist = $this->getBlacklistedFolders();
        $rootNamespace = $rootNamespace == PackageList::ROOT ? '' : $rootNamespace;

        $pathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities('UTF-8')));
        $path = $pathBuilder->namespaceToFullPath($rootNamespace);

        $finder = new Finder();
        $finder->depth('== 0')->directories()->in($path);

        foreach ($finder as $folder)
        {
            if (!in_array($folder, $blacklist) && !str_starts_with($folder, '.'))
            {
                $folderNamespace = ($rootNamespace ? $rootNamespace . '\\' : '') . $folder;

                if ($this->verifyPackage($folderNamespace))
                {
                    $this->addPackageNamespace($folderNamespace);
                }

                $this->discoverPackages($folderNamespace);
            }
        }
    }

    /**
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return ['.hg', '.git', 'build', 'Build', 'plugin', 'resources', 'Resources', 'Test'];
    }

    /**
     * @return string[]
     */
    public function getPackageNamespaces()
    {
        return $this->packageNamespaces;
    }

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->rootNamespace;
    }

    protected function setup(): void
    {
        $this->discoverPackages($this->rootNamespace);
    }

    /**
     * @param string $folderNamespace
     *
     * @return bool
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities('UTF-8')));
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/composer.json';

        return file_exists($packageInfoPath);
    }
}
