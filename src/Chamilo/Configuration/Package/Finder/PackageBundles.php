<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundles extends BasicBundles
{

    /**
     *
     * @var integer
     */
    private $mode;

    /**
     *
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    private $packageDefinitions;

    /**
     *
     * @var \Chamilo\Configuration\Package\PackageList[]
     */
    private $packageLists = array();

    /**
     *
     * @param string $namespace
     * @param integer $mode
     */
    public function __construct($rootNamespace = PackageList :: ROOT, $mode = PackageList :: MODE_ALL)
    {
        $this->mode = $mode;
        parent::__construct($rootNamespace);
    }

    protected function setup()
    {
        parent::setup();
        $this->readPackageDefinitions();
        $this->processPackageTypes();
    }

    /**
     *
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return array('.hg', 'build', 'Build', 'plugin', 'resources', 'Resources', 'Test');
    }

    /**
     *
     * @param string $folderNamespace
     * @return boolean
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/package.info';
        return file_exists($packageInfoPath);
    }

    private function readPackageDefinitions()
    {
        foreach ($this->getPackageNamespaces() as $packageNamespace)
        {
            $packageDefinition = Package::get($packageNamespace);
            $this->packageDefinitions[$packageNamespace] = $packageDefinition;
        }
    }

    private function processPackageTypes()
    {
        foreach ($this->getPackageNamespaces() as $packageNamespace)
        {

            $packageNamespaceAncestors = $this->determinePackageNamespaceAncestors($packageNamespace);
            $packageNamespaceParent = array_shift($packageNamespaceAncestors);

            if (! isset($this->packageLists[$packageNamespaceParent]))
            {
                $this->setPackageList($packageNamespaceParent);
            }

            if ($this->isRelevantPackage($packageNamespace) &&
                 ! $this->packageLists[$packageNamespaceParent]->has_package($packageNamespace))
            {
                $this->packageLists[$packageNamespaceParent]->add_package($this->packageDefinitions[$packageNamespace]);
            }

            $previousPackageList = $this->packageLists[$packageNamespaceParent];

            foreach ($packageNamespaceAncestors as $packageNamespaceAncestor)
            {
                if (! isset($this->packageLists[$packageNamespaceAncestor]))
                {
                    $this->setPackageList($packageNamespaceAncestor);
                }

                if (! $this->packageLists[$packageNamespaceAncestor]->has_child($previousPackageList->get_type()))
                {
                    $this->packageLists[$packageNamespaceAncestor]->add_child($previousPackageList);
                }

                $previousPackageList = $this->packageLists[$packageNamespaceAncestor];
            }
        }
    }

    /**
     *
     * @return boolean
     */
    protected function isRelevantPackage($packageNamespace)
    {
        $isAll = $this->mode == PackageList::MODE_ALL;
        $isInstalled = $this->mode == PackageList::MODE_INSTALLED &&
             \Chamilo\Configuration\Configuration::is_registered($packageNamespace);
        $isAvailable = $this->mode == PackageList::MODE_AVAILABLE &&
             ! \Chamilo\Configuration\Configuration::is_registered($packageNamespace);

        return $isAll || $isInstalled || $isAvailable;
    }

    /**
     *
     * @param string $packageNamespace
     */
    public function setPackageList($packageNamespace)
    {
        if ($packageNamespace === PackageList::ROOT)
        {
            $typeName = Translation::get('Platform');
            $packageImageNamespace = 'Chamilo\Configuration';
        }
        else
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($packageNamespace);
            $packageImageNamespace = $packageNamespace;
        }

        $iconPath = Theme::getInstance()->getImagePath($packageImageNamespace, 'Logo/16', 'png', false);

        if (file_exists($iconPath))
        {
            $iconPath = Theme::getInstance()->getImagePath($packageImageNamespace, 'Logo/16');
        }
        else
        {
            $iconPath = null;
        }

        $this->packageLists[$packageNamespace] = new PackageList($packageNamespace, $typeName, $iconPath);
    }

    /**
     *
     * @param string $packageNamespace
     * @return string[]
     */
    private function determinePackageNamespaceAncestors($packageNamespace)
    {
        $packageNamespacePath = array();
        $packageParentNamespace = $this->determinePackageParentNamespace($packageNamespace);
        $packagePath[] = $packageParentNamespace;

        while ($packageParentNamespace != PackageList::ROOT)
        {
            $packageParentNamespace = $this->determinePackageParentNamespace($packageParentNamespace);
            $packagePath[] = $packageParentNamespace;
        }

        return $packagePath;
    }

    /**
     *
     * @param string $packageNamespace
     * @return string
     */
    private function determinePackageParentNamespace($packageNamespace)
    {
        if (isset($this->packageDefinitions[$packageNamespace]))
        {
            return $this->packageDefinitions[$packageNamespace]->get_type();
        }
        else
        {
            $packageParentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($packageNamespace);
            return $packageParentNamespace ? $packageParentNamespace : PackageList::ROOT;
        }
    }

    public function getPackageList()
    {
        return $this->packageLists[$this->getRootNamespace()];
    }
}
