<?php
namespace Chamilo\Core\Admin\Service\Finder;

use Chamilo\Core\Admin\Service\PackageFactory;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;

/**
 * @package Chamilo\Core\Admin\Service\Finder
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
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
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
