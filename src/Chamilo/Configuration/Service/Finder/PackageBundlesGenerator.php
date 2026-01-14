<?php
namespace Chamilo\Configuration\Service\Finder;

use Chamilo\Configuration\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;

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

    public function __construct(
        SystemPathBuilder $systemPathBuilder, ClassnameUtilities $classnameUtilities, PackageFactory $packageFactory
    )
    {
        parent::__construct($systemPathBuilder);

        $this->classnameUtilities = $classnameUtilities;
        $this->packageFactory = $packageFactory;
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
     * @return \Chamilo\Configuration\Storage\DataClass\Package[]
     */
    public function getPackages(): array
    {
        $packages = [];

        foreach ($this->getPackageNamespaces() as $packageNamespace)
        {
            $packages[$packageNamespace] = $this->getPackageFactory()->getPackage($packageNamespace);
        }

        return $packages;
    }
}
