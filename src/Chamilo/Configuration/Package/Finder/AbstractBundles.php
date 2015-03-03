<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Configuration\Package\PackageList;

/**
 *
 * @package Chamilo\Configuration\Package\Builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractBundles
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
    public function __construct($rootNamespace = PackageList :: ROOT)
    {
        $this->rootNamespace = $rootNamespace;
        $this->setup();
    }

    protected function setup()
    {
        $this->discoverPackages($this->rootNamespace);
    }

    public function getPackageNamespaces()
    {
        return $this->packageNamespaces;
    }

    protected function addPackageNamespace($packageNamespace)
    {
        return $this->packageNamespaces[] = $packageNamespace;
    }

    /**
     *
     * @param string $namespace
     */
    private function discoverPackages($rootNamespace)
    {
        $blacklist = $this->getBlacklistedFolders();
        $rootNamespace = $rootNamespace == PackageList :: ROOT ? '' : $rootNamespace;

        $path = Path :: getInstance()->namespaceToFullPath($rootNamespace);

        $folders = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);

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
    abstract protected function getBlacklistedFolders();

    /**
     *
     * @param string $folderNamespace
     * @return boolean
     */
    abstract protected function verifyPackage($folderNamespace);
}
