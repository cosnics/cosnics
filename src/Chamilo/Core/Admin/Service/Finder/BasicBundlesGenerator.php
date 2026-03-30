<?php
namespace Chamilo\Core\Admin\Service\Finder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class BasicBundlesGenerator extends AbstractBundlesGenerator
{
    protected function verifyPackage(string $folderNamespace): bool
    {
        $packageInfoPath = $this->systemPathBuilder->namespaceToFullPath($folderNamespace) . '/composer.json';

        return file_exists($packageInfoPath);
    }
}
