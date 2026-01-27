<?php
namespace Chamilo\Core\Admin\Service\Finder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InternationalizationBundlesGenerator extends AbstractBundlesGenerator
{

    protected function verifyPackage(string $folderNamespace): bool
    {
        $i18nPath = $this->getSystemPathBuilder()->getI18nPath($folderNamespace);

        return file_exists($i18nPath) && is_dir($i18nPath);
    }
}