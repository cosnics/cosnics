<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InternationalizationBundles extends BasicBundles
{

    /**
     *
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return array('.hg', 'build', 'Build', 'plugin', 'Plugin', 'Test', 'resources', 'Resources');
    }

    /**
     *
     * @param string $folderNamespace
     * @return boolean
     */
    protected function verifyPackage($folderNamespace)
    {
        $i18nPath = Path :: getInstance()->getI18nPath($folderNamespace);
        return file_exists($i18nPath) && is_dir($i18nPath);
    }
}
