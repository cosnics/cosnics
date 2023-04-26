<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundles extends BasicBundles
{

    /**
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return ['.hg', 'build', 'Build', 'plugin', 'Plugin', 'Test', 'resources', 'Resources'];
    }

    /**
     * @param string $folderNamespace
     *
     * @return bool
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new SystemPathBuilder(ClassnameUtilities::getInstance());

        $i18nPath = $pathBuilder->getI18nPath($folderNamespace);

        return file_exists($i18nPath) && is_dir($i18nPath);
    }
}
