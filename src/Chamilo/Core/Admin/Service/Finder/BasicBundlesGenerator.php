<?php
namespace Chamilo\Core\Admin\Service\Finder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BasicBundlesGenerator extends AbstractBundlesGenerator
{

    protected function verifyPackage(string $folderNamespace): bool
    {
        $packageInfoPath = $this->getSystemPathBuilder()->namespaceToFullPath($folderNamespace) . '/composer.json';

        return file_exists($packageInfoPath);
    }
}
