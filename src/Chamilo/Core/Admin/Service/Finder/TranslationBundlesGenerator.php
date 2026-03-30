<?php
namespace Chamilo\Core\Admin\Service\Finder;

/**
 * @package Chamilo\Core\Admin\Service\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class TranslationBundlesGenerator extends AbstractBundlesGenerator
{
    protected function verifyPackage(string $folderNamespace): bool
    {
        $translationPath = $this->systemPathBuilder->getTranslationPath($folderNamespace);

        return file_exists($translationPath) && is_dir($translationPath);
    }
}