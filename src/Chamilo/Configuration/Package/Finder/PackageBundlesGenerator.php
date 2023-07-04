<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackageBundlesGenerator extends BasicBundlesGenerator
{

    protected ClassnameUtilities $classnameUtilities;

    protected PackageFactory $packageFactory;

    protected RegistrationConsulter $registrationConsulter;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, ClassnameUtilities $classnameUtilities, PackageFactory $packageFactory,
        RegistrationConsulter $registrationConsulter
    )
    {
        parent::__construct($systemPathBuilder);

        $this->classnameUtilities = $classnameUtilities;
        $this->packageFactory = $packageFactory;
        $this->registrationConsulter = $registrationConsulter;
    }

    protected function determinePackageNamespaceAncestors(array $packageDefinitions, string $packageNamespace): array
    {
        $packageParentNamespace = $this->determinePackageParentNamespace($packageDefinitions, $packageNamespace);
        $packagePath[] = $packageParentNamespace;

        while ($packageParentNamespace != PackageList::ROOT)
        {
            $packageParentNamespace =
                $this->determinePackageParentNamespace($packageDefinitions, $packageParentNamespace);
            $packagePath[] = $packageParentNamespace;
        }

        return $packagePath;
    }

    protected function determinePackageParentNamespace(array $packageDefinitions, string $packageNamespace): string
    {
        if (isset($packageDefinitions[$packageNamespace]))
        {
            return $packageDefinitions[$packageNamespace]->getType();
        }
        else
        {
            $packageParentNamespace = $this->getClassnameUtilities()->getNamespaceParent($packageNamespace);

            return $packageParentNamespace ?: PackageList::ROOT;
        }
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getPackageListForNamespaceAndMode(
        string $namespace = PackageList::ROOT, int $mode = PackageList::MODE_ALL
    ): PackageList
    {
        $packageLists = $this->getPackageListsForNamespaceAndMode($namespace, $mode);

        return $packageLists[$namespace];
    }

    protected function getPackageListForPackageNamespace(string $packageNamespace): PackageList
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

        return new PackageList($packageNamespace, $typeName, $glyph);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    protected function getPackageListsForNamespaceAndMode(string $namespace, int $mode): array
    {
        $packageNamespaces = $this->getPackageNamespacesForNamespace($namespace);
        $packageDefinitions = $this->readPackageDefinitionsForNamespace($namespace);

        $packageLists = [];

        foreach ($packageNamespaces as $packageNamespace)
        {
            $packageNamespaceAncestors =
                $this->determinePackageNamespaceAncestors($packageDefinitions, $packageNamespace);
            $packageNamespaceParent = array_shift($packageNamespaceAncestors);

            if (!isset($packageLists[$packageNamespaceParent]))
            {
                $packageLists[$packageNamespaceParent] =
                    $this->getPackageListForPackageNamespace($packageNamespaceParent);
            }

            if ($this->isRelevantPackage($packageNamespace, $mode) &&
                !$packageLists[$packageNamespaceParent]->hasPackage($packageNamespace))
            {
                $packageLists[$packageNamespaceParent]->addPackage($packageDefinitions[$packageNamespace]);
            }

            $previousPackageList = $packageLists[$packageNamespaceParent];

            foreach ($packageNamespaceAncestors as $packageNamespaceAncestor)
            {
                if (!isset($packageLists[$packageNamespaceAncestor]))
                {
                    $packageLists[$packageNamespaceAncestor] =
                        $this->getPackageListForPackageNamespace($packageNamespaceAncestor);
                }

                if (!$packageLists[$packageNamespaceAncestor]->hasPackageList($previousPackageList->getType()))
                {
                    $packageLists[$packageNamespaceAncestor]->addPackageList($previousPackageList);
                }

                $previousPackageList = $packageLists[$packageNamespaceAncestor];
            }
        }

        return $packageLists;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function isRelevantPackage(string $packageNamespace, int $mode): bool
    {
        $registrationConsulter = $this->getRegistrationConsulter();

        $isAll = $mode == PackageList::MODE_ALL;
        $isInstalled =
            $mode == PackageList::MODE_INSTALLED && $registrationConsulter->isContextRegistered($packageNamespace);
        $isAvailable =
            $mode == PackageList::MODE_AVAILABLE && !$registrationConsulter->isContextRegistered($packageNamespace);

        return $isAll || $isInstalled || $isAvailable;
    }

    /**
     * @throws \Exception
     */
    public function readPackageDefinitionsForNamespace(string $namespace = PackageList::ROOT): array
    {
        $packageDefinitions = [];

        foreach ($this->getPackageNamespacesForNamespace($namespace) as $packageNamespace)
        {
            $packageDefinition = $this->getPackageFactory()->getPackage($packageNamespace);
            $packageDefinitions[$packageNamespace] = $packageDefinition;
        }

        return $packageDefinitions;
    }
}
