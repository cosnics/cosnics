<?php
namespace Chamilo\Core\Admin\Service\Finder;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractBundlesGenerator
{
    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
    }

    public function getPackageNamespaces(): array
    {
        $packagesListPath = $this->getSystemPathBuilder()->getStoragePath() . 'configuration' . DIRECTORY_SEPARATOR .
            'configuration.packages.json';
        $packagesList = json_decode(file_get_contents($packagesListPath));

        $packages = $packagesList->packages;

        $packageNamespaces = [];

        foreach ($packages as $package) {
            if ($this->verifyPackage($package)) {
                $packageNamespaces[] = $package;
            }
        }

        return $packageNamespaces;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    abstract protected function verifyPackage(string $folderNamespace): bool;
}
