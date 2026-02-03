<?php
namespace Chamilo\Libraries\UserInterface\Translation\Service;

use Chamilo\Libraries\Filesystem\Service\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\UserInterface\Translation\Architecture\Interface\TranslationResourcesFinderInterface;

/**
 * Implementation of the translation resources finder which scans chamilo packages for translation resources
 *
 * @package Chamilo\Libraries\UserInterface\Translation\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackagesTranslationResourcesFinder implements TranslationResourcesFinderInterface
{
    private PackagesFilesFinder $packagesFilesFinder;

    public function __construct(PackagesFilesFinder $packagesFilesFinder)
    {
        $this->packagesFilesFinder = $packagesFilesFinder;
    }

    /**
     * @return string[]
     * @throws \Exception
     * @example $resource['nl']['domain'] = '/path/to/resource'
     */
    public function findTranslationResources(): array
    {
        $resources = [];

        $translationFiles = $this->packagesFilesFinder->findFiles('Resources/I18n/', '/.*\.i18n$/');

        foreach ($translationFiles as $package => $translationFilesPerPackage) {
            foreach ($translationFilesPerPackage as $translationFile) {
                $fileParts = explode('.', basename($translationFile));
                $resources[$fileParts[0]][$package] = $translationFile;
            }
        }

        return $resources;
    }
}