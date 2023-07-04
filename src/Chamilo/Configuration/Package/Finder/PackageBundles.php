<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundles extends BasicBundles
{

    protected PackageFactory $packageFactory;

    private int $mode;

    /**
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    private array $packageDefinitions;

    /**
     * @var \Chamilo\Configuration\Package\PackageList[]
     */
    private array $packageLists = [];

    public function __construct(
        string $rootNamespace = PackageList::ROOT, int $mode = PackageList::MODE_ALL,
        PackageFactory $packageFactory = null
    )
    {
        $this->mode = $mode;
        $this->packageFactory = $packageFactory;
        parent::__construct($rootNamespace);
    }

    private function determinePackageNamespaceAncestors(string $packageNamespace): array
    {
        $packageParentNamespace = $this->determinePackageParentNamespace($packageNamespace);
        $packagePath[] = $packageParentNamespace;

        while ($packageParentNamespace != PackageList::ROOT)
        {
            $packageParentNamespace = $this->determinePackageParentNamespace($packageParentNamespace);
            $packagePath[] = $packageParentNamespace;
        }

        return $packagePath;
    }

    private function determinePackageParentNamespace(string $packageNamespace): string
    {
        if (isset($this->packageDefinitions[$packageNamespace]))
        {
            return $this->packageDefinitions[$packageNamespace]->getType();
        }
        else
        {
            $packageParentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($packageNamespace);

            return $packageParentNamespace ?: PackageList::ROOT;
        }
    }

    public function getPackageList(): PackageList
    {
        return $this->packageLists[$this->getRootNamespace()];
    }

    protected function isRelevantPackage(string $packageNamespace): bool
    {
        $isAll = $this->mode == PackageList::MODE_ALL;
        $isInstalled = $this->mode == PackageList::MODE_INSTALLED && Configuration::is_registered($packageNamespace);
        $isAvailable = $this->mode == PackageList::MODE_AVAILABLE && !Configuration::is_registered($packageNamespace);

        return $isAll || $isInstalled || $isAvailable;
    }

    private function processPackageTypes(): void
    {
        foreach ($this->getPackageNamespaces() as $packageNamespace)
        {

            $packageNamespaceAncestors = $this->determinePackageNamespaceAncestors($packageNamespace);
            $packageNamespaceParent = array_shift($packageNamespaceAncestors);

            if (!isset($this->packageLists[$packageNamespaceParent]))
            {
                $this->setPackageList($packageNamespaceParent);
            }

            if ($this->isRelevantPackage($packageNamespace) &&
                !$this->packageLists[$packageNamespaceParent]->hasPackage($packageNamespace))
            {
                $this->packageLists[$packageNamespaceParent]->addPackage($this->packageDefinitions[$packageNamespace]);
            }

            $previousPackageList = $this->packageLists[$packageNamespaceParent];

            foreach ($packageNamespaceAncestors as $packageNamespaceAncestor)
            {
                if (!isset($this->packageLists[$packageNamespaceAncestor]))
                {
                    $this->setPackageList($packageNamespaceAncestor);
                }

                if (!$this->packageLists[$packageNamespaceAncestor]->hasPackageList($previousPackageList->getType()))
                {
                    $this->packageLists[$packageNamespaceAncestor]->addPackageList($previousPackageList);
                }

                $previousPackageList = $this->packageLists[$packageNamespaceAncestor];
            }
        }
    }

    private function readPackageDefinitions(): void
    {
        foreach ($this->getPackageNamespaces() as $packageNamespace)
        {
            $packageDefinition = $this->packageFactory->getPackage($packageNamespace);
            $this->packageDefinitions[$packageNamespace] = $packageDefinition;
        }
    }

    public function setPackageList(string $packageNamespace): void
    {
        if ($packageNamespace === PackageList::ROOT)
        {
            $typeName = 'Platform';
            $packageImageNamespace = 'Chamilo\Configuration';
        }
        else
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($packageNamespace);
            $packageImageNamespace = $packageNamespace;
        }

        $glyph = new NamespaceIdentGlyph(
            $packageImageNamespace, false, false, false, IdentGlyph::SIZE_MINI, ['fa-fw']
        );

        $this->packageLists[$packageNamespace] = new PackageList($packageNamespace, $typeName, $glyph);
    }

    protected function setup(): void
    {
        parent::setup();
        $this->readPackageDefinitions();
        $this->processPackageTypes();
    }
}
