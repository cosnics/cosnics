<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BasicBundles
{

    /**
     *
     * @var string
     */
    private $rootNamespace;

    /**
     *
     * @var string[]
     */
    private $packageNamespaces = array();

    /**
     *
     * @param string $namespace
     * @param integer $mode
     */
    public function __construct($rootNamespace = PackageList::ROOT)
    {
        $this->rootNamespace = $rootNamespace;
        $this->setup();
    }

    protected function setup()
    {
        $this->discoverPackages($this->rootNamespace);
    }

    /**
     *
     * @return string[]
     */
    public function getPackageNamespaces()
    {
        return $this->packageNamespaces;
    }

    /**
     *
     * @param string $packageNamespace
     */
    protected function addPackageNamespace($packageNamespace)
    {
        $this->packageNamespaces[] = $packageNamespace;
    }

    /**
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->rootNamespace;
    }

    /**
     *
     * @param string $namespace
     */
    private function discoverPackages($rootNamespace)
    {
        $blacklist = $this->getBlacklistedFolders();
        $rootNamespace = $rootNamespace == PackageList::ROOT ? '' : $rootNamespace;

        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());
        $path = $pathBuilder->namespaceToFullPath($rootNamespace);

        $folders = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);

        foreach ($folders as $folder)
        {
            if (! in_array($folder, $blacklist) && substr($folder, 0, 1) != '.')
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
     *
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return array('.hg', '.git', 'build', 'Build', 'plugin', 'resources', 'Resources', 'Test');
    }

    /**
     *
     * @param string $folderNamespace
     * @return boolean
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/composer.json';
        return file_exists($packageInfoPath);
    }
}
