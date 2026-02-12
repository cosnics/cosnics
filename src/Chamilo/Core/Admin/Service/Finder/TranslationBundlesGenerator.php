<?php
namespace Chamilo\Core\Admin\Service\Finder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationBundlesGenerator extends AbstractBundlesGenerator
{
    protected function verifyPackage(string $folderNamespace): bool
    {
        $translationPath = $this->getSystemPathBuilder()->getTranslationPath($folderNamespace);

        return file_exists($translationPath) && is_dir($translationPath);
    }
}