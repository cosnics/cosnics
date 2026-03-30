<?php
namespace Chamilo\Core\Admin\Service\Finder;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract readonly class AbstractBundlesGenerator
{
    public function __construct(protected SystemPathBuilder $systemPathBuilder)
    {
    }

    public function getPackageNamespaces(): array
    {
        $packagesListPath = $this->systemPathBuilder->getStoragePath() . 'configuration' . DIRECTORY_SEPARATOR .
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

    abstract protected function verifyPackage(string $folderNamespace): bool;
}
