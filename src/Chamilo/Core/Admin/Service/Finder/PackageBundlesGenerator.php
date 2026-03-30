<?php
namespace Chamilo\Core\Admin\Service\Finder;

use Chamilo\Core\Admin\Service\PackageFactory;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class PackageBundlesGenerator extends BasicBundlesGenerator
{
    public function __construct(
        SystemPathBuilder $systemPathBuilder, protected PackageFactory $packageFactory
    )
    {
        parent::__construct($systemPathBuilder);
    }

    /**
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
     */
    public function getPackages(): array
    {
        $packages = [];

        foreach ($this->getPackageNamespaces() as $packageNamespace) {
            $packages[$packageNamespace] = $this->packageFactory->getPackage($packageNamespace);
        }

        return $packages;
    }
}
