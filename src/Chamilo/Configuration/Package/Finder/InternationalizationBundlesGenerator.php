<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InternationalizationBundlesGenerator extends AbstractBundlesGenerator
{

    protected function verifyPackage(string $folderNamespace): bool
    {
        $pathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities('UTF-8')));

        $i18nPath = $pathBuilder->getI18nPath($folderNamespace);

        return file_exists($i18nPath) && is_dir($i18nPath);
    }
}